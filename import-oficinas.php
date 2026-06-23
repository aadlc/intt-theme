<?php
/**
 * Script de importación de oficinas regionales INTT.
 * Uso: wp eval-file import-oficinas.php
 */

// Claves de campos ACF
define( 'ACF_DIRECCION',  'field_6a39bf328b427' );
define( 'ACF_JEFE',       'field_6a39bf5e8b428' );
define( 'ACF_INSTAGRAM',  'field_6a39bf928b429' );
define( 'ACF_X',          'field_6a39bfa58b42a' );
define( 'ACF_TIKTOK',     'field_6a39bfb78b42b' );
define( 'ACF_YOUTUBE',    'field_6a39bfc38b42c' );

if ( ! function_exists( 'update_field' ) ) {
    WP_CLI::error( 'ACF no está activo.' );
    return;
}

$json = file_get_contents( __DIR__ . '/import-oficinas.json' );
$oficinas = json_decode( $json, true );

if ( ! $oficinas ) {
    WP_CLI::error( 'No se pudo leer import-oficinas.json.' );
    return;
}

$creadas  = 0;
$omitidas = 0;

foreach ( $oficinas as $oficina ) {

    // Evitar duplicados
    $existe = new WP_Query( [
        'post_type'      => 'oficina',
        'post_status'    => 'any',
        'title'          => $oficina['titulo'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ] );

    if ( $existe->have_posts() ) {
        WP_CLI::log( "Omitida (ya existe): {$oficina['titulo']}" );
        $omitidas++;
        continue;
    }

    // Crear post
    $post_id = wp_insert_post( [
        'post_title'  => $oficina['titulo'],
        'post_type'   => 'oficina',
        'post_status' => 'publish',
    ], true );

    if ( is_wp_error( $post_id ) ) {
        WP_CLI::warning( "Error al crear: {$oficina['titulo']} — {$post_id->get_error_message()}" );
        continue;
    }

    // Campos ACF
    update_field( ACF_DIRECCION, $oficina['direccion'], $post_id );
    update_field( ACF_JEFE,      $oficina['jefe'],      $post_id );

    if ( $oficina['instagram'] ) {
        update_field( ACF_INSTAGRAM, $oficina['instagram'], $post_id );
    }

    if ( $oficina['x'] ) {
        update_field( ACF_X, [ 'url' => $oficina['x'], 'title' => '', 'target' => '_blank' ], $post_id );
    }

    if ( $oficina['tiktok'] ) {
        update_field( ACF_TIKTOK, $oficina['tiktok'], $post_id );
    }

    if ( $oficina['youtube'] ) {
        update_field( ACF_YOUTUBE, $oficina['youtube'], $post_id );
    }

    // Taxonomía estado
    if ( ! empty( $oficina['estado'] ) ) {
        $term = get_term_by( 'name', $oficina['estado'], 'estado' );
        if ( ! $term ) {
            $result  = wp_insert_term( $oficina['estado'], 'estado' );
            $term_id = is_wp_error( $result ) ? null : $result['term_id'];
        } else {
            $term_id = $term->term_id;
        }
        if ( $term_id ) {
            wp_set_post_terms( $post_id, [ $term_id ], 'estado' );
        }
    }

    WP_CLI::log( "Creada: {$oficina['titulo']} (ID: {$post_id})" );
    $creadas++;
}

WP_CLI::success( "Importación completada — {$creadas} creadas, {$omitidas} omitidas." );
