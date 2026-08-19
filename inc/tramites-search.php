<?php
/**
 * Búsqueda de trámites integrada en el archivo del CPT.
 *
 * El bloque wp:search del archivo envía a /?s=xxx&post_type=tramite.
 * Por defecto WP usaría search.html (o index.html) para renderizar esos
 * resultados. El filtro template_include fuerza el uso de archive-tramite.html
 * para conservar el sidebar de categorías y todo el layout del archivo.
 *
 * El query loop del archivo usa inherit:true, por lo que hereda la query
 * principal — que ya incluye 's' cuando la URL trae el parámetro de búsqueda.
 */

add_filter( 'template_include', function ( $template ) {

    if ( ! is_search() ) {
        return $template;
    }

    $post_type = isset( $_GET['post_type'] ) ? sanitize_key( wp_unslash( $_GET['post_type'] ) ) : '';
    if ( $post_type !== 'tramite' ) {
        return $template;
    }

    $archive = get_query_template( 'archive-tramite' );
    return $archive ?: $template;
} );
