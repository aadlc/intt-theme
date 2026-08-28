<?php
/**
 * Importador de la biblioteca de documentos a WordPress.
 *
 * Ejecutar con WP-CLI:
 *   wp eval-file tools/biblioteca-migration/import.php
 *
 * Lee manifest.json y para cada item que tenga ruta_local:
 *   1. Salta si ya existe un post con el mismo slug (idempotente)
 *   2. Copia el PDF a wp-content/uploads/ como adjunto
 *   3. Crea el post CPT 'documento' con el título
 *   4. Setea ACF field 'archivo' al adjunto
 *   5. Asigna término de categoria_documento por slug
 *
 * Ejemplo dry-run (no escribe nada). En PowerShell:
 *   $env:BIBLIOTECA_DRY_RUN='1'; wp eval-file tools/biblioteca-migration/import.php
 * En Bash:
 *   BIBLIOTECA_DRY_RUN=1 wp eval-file tools/biblioteca-migration/import.php
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    die( "Este script debe ejecutarse con WP-CLI: wp eval-file " . basename( __FILE__ ) . "\n" );
}

// Dry-run y limit vía env var (wp eval-file no permite flags custom en la línea).
$dry_run = ! empty( getenv( 'BIBLIOTECA_DRY_RUN' ) );
$limit   = (int) getenv( 'BIBLIOTECA_LIMIT' );

$manifest_path = __DIR__ . '/manifest.json';
if ( ! file_exists( $manifest_path ) ) {
    WP_CLI::error( "No encontré manifest.json en $manifest_path" );
}

$items = json_decode( file_get_contents( $manifest_path ), true );
if ( ! is_array( $items ) ) {
    WP_CLI::error( "manifest.json no es un array JSON válido" );
}

if ( ! taxonomy_exists( 'categoria_documento' ) ) {
    WP_CLI::error( "La taxonomía categoria_documento no está registrada. ¿Está activo el tema intt-theme y ACF cargó el registro?" );
}
if ( ! post_type_exists( 'documento' ) ) {
    WP_CLI::error( "El CPT documento no está registrado. Verifica ACF y el tema." );
}
if ( ! function_exists( 'update_field' ) ) {
    WP_CLI::error( "ACF no está disponible. Verifica que el plugin esté activo." );
}

WP_CLI::line( "→ manifest: " . count( $items ) . " items" );
WP_CLI::line( "→ dry-run:  " . ( $dry_run ? 'sí' : 'no' ) );
WP_CLI::line( "→ limit:    " . ( $limit > 0 ? $limit : 'sin límite' ) );

require_once ABSPATH . 'wp-admin/includes/image.php';

$ok            = 0;
$saltados      = 0;
$fallos        = 0;
$sin_descargar = 0;
$procesados    = 0;

foreach ( $items as $i => $doc ) {

    if ( $limit > 0 && $procesados >= $limit ) break;
    $procesados++;

    $num = sprintf( '[%3d/%d]', $i + 1, count( $items ) );
    $titulo_corto = mb_substr( $doc['titulo'], 0, 70 );
    WP_CLI::line( "\n$num $titulo_corto" );

    if ( empty( $doc['ruta_local'] ) || ! file_exists( $doc['ruta_local'] ) ) {
        WP_CLI::line( "           ↷ sin PDF local (falta descargar)" );
        $sin_descargar++;
        continue;
    }

    // Idempotencia: buscar post existente por slug
    $slug = wp_unique_post_slug(
        sanitize_title( $doc['titulo'] ),
        0,
        'publish',
        'documento',
        0
    );
    // sanitize_title() sin unique — para verificar existencia real
    $slug_puro = sanitize_title( $doc['titulo'] );

    $existente = get_page_by_path( $slug_puro, OBJECT, 'documento' );
    if ( $existente ) {
        WP_CLI::line( "           ↷ ya existe (post ID {$existente->ID})" );
        $saltados++;
        continue;
    }

    if ( $dry_run ) {
        WP_CLI::line( "           dry-run: crearía post + adjuntaría " . basename( $doc['ruta_local'] ) . " + categoría {$doc['categoria']}" );
        continue;
    }

    try {
        // 1. Copiar PDF a uploads/
        $upload_dir = wp_upload_dir();
        $filename   = basename( $doc['ruta_local'] );
        $destino    = trailingslashit( $upload_dir['path'] ) . wp_unique_filename( $upload_dir['path'], $filename );

        if ( ! copy( $doc['ruta_local'], $destino ) ) {
            throw new Exception( "No pude copiar el PDF a $destino" );
        }

        $filetype = wp_check_filetype( basename( $destino ) );
        $attach_id = wp_insert_attachment( [
            'guid'           => trailingslashit( $upload_dir['url'] ) . basename( $destino ),
            'post_mime_type' => $filetype['type'] ?: 'application/pdf',
            'post_title'     => sanitize_text_field( $doc['titulo'] ),
            'post_content'   => '',
            'post_status'    => 'inherit',
        ], $destino );

        if ( is_wp_error( $attach_id ) ) {
            throw new Exception( "wp_insert_attachment: " . $attach_id->get_error_message() );
        }

        wp_update_attachment_metadata( $attach_id, wp_generate_attachment_metadata( $attach_id, $destino ) );

        // 2. Crear post CPT documento
        $post_id = wp_insert_post( [
            'post_type'   => 'documento',
            'post_status' => 'publish',
            'post_title'  => $doc['titulo'],
            'post_name'   => $slug_puro,
        ], true );

        if ( is_wp_error( $post_id ) ) {
            throw new Exception( "wp_insert_post: " . $post_id->get_error_message() );
        }

        // 3. Enlazar ACF field 'archivo' → attachment ID
        update_field( 'archivo', $attach_id, $post_id );

        // 4. Asignar categoría
        $term = get_term_by( 'slug', $doc['categoria'], 'categoria_documento' );
        if ( ! $term ) {
            WP_CLI::warning( "Categoría '{$doc['categoria']}' no existe. Post creado sin categoría." );
        } else {
            wp_set_object_terms( $post_id, [ (int) $term->term_id ], 'categoria_documento' );
        }

        WP_CLI::line( "           ✓ post $post_id, attachment $attach_id" );
        $ok++;

    } catch ( Exception $e ) {
        WP_CLI::warning( "           ✗ {$e->getMessage()}" );
        $fallos++;
    }
}

WP_CLI::line( "" );
WP_CLI::success( "ok=$ok, saltados=$saltados, sin-descargar=$sin_descargar, fallos=$fallos" );
