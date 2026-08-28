<?php
/**
 * CPT documento — biblioteca de documentos institucionales.
 *
 * El CPT y la taxonomía categoria_documento se registran vía ACF UI
 * (ver acf-json/post_type_6a90c43c41419.json y taxonomy_6a90c49fa831e.json).
 * Este archivo solo agrega comportamiento.
 */

// ── Desactivar el archive del CPT ────────────────────────────────────────────
// La URL /biblioteca/ es servida por una WP Page (creada en inc/default-pages.php)
// con contenido editable, no por el archive del CPT. Este filtro anula el
// has_archive: true que viene de la config ACF UI, para liberar la URL.

add_filter( 'register_post_type_args', function ( $args, $post_type ) {
    if ( $post_type === 'documento' ) {
        $args['has_archive'] = false;
    }
    return $args;
}, 10, 2 );

// ── Categorías por defecto ────────────────────────────────────────────────────
// Se crean si no existen. Orden fijo definido por el array — es el mismo orden
// que se usa en el bloque intt/biblioteca-list para renderizar las secciones.

const INTT_CATEGORIAS_DOCUMENTO = [
    'libros-varios-relacionados'               => 'Libros Varios Relacionados',
    'manuales-varios-relacionados'             => 'Manuales Varios Relacionados',
    'normas-tecnicas-relacionadas'             => 'Normas Técnicas Relacionadas',
    'reglamentos-tecnicos-relacionados'        => 'Reglamentos Técnicos Relacionados',
    'documentos-comite-expertos-onu'           => 'Documentos del Comité de Expertos en Transporte Seguro de Mercancías Peligrosas – ONU',
    'manual-venezolano-dispositivos-uniformes' => 'Manual Venezolano de Dispositivos Uniformes para el Control del Tránsito',
];

add_action( 'after_switch_theme', 'intt_crear_categorias_documento' );
add_action( 'admin_init',         'intt_crear_categorias_documento' );

function intt_crear_categorias_documento() {
    if ( ! taxonomy_exists( 'categoria_documento' ) ) return;

    foreach ( INTT_CATEGORIAS_DOCUMENTO as $slug => $nombre ) {
        if ( ! term_exists( $slug, 'categoria_documento' ) ) {
            wp_insert_term( $nombre, 'categoria_documento', [ 'slug' => $slug ] );
        }
    }
}

// ── Redirigir single → PDF ────────────────────────────────────────────────────
// El documento no tiene página propia: al visitar /documento/xxx/ el navegador
// recibe el PDF directamente (lo abre inline o lo descarga según headers).

add_action( 'template_redirect', function () {
    if ( ! is_singular( 'documento' ) ) return;
    if ( ! function_exists( 'get_field' ) ) return;

    $archivo = get_field( 'archivo', get_the_ID() );
    if ( ! empty( $archivo['url'] ) ) {
        wp_redirect( $archivo['url'], 302 );
        exit;
    }
} );

// ── Orden A-Z en el archive ───────────────────────────────────────────────────
// Red de seguridad. El bloque intt/biblioteca-list corre su propia query,
// pero si alguien inserta un Query Loop nativo apuntando a documentos,
// que salgan ordenados.

add_action( 'pre_get_posts', function ( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( ! $query->is_post_type_archive( 'documento' ) ) return;
    if ( $query->is_search() ) return;

    $query->set( 'orderby',        'title' );
    $query->set( 'order',          'ASC' );
    $query->set( 'posts_per_page', -1 );
} );
