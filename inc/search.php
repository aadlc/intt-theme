<?php
/**
 * Búsqueda global del sitio.
 *
 * Si alguien llega a /?s= con query vacía (URL escrita a mano, bookmark
 * antiguo, etc.), redirige a /buscar/ que es la landing dedicada. Los
 * resultados propiamente dichos se renderizan en templates/search.html,
 * alcanzables solo con /?s=algo.
 */

add_action( 'template_redirect', function () {
    if ( ! is_search() ) return;

    $s = trim( (string) get_search_query() );
    if ( '' !== $s ) return;

    $buscar = get_page_by_path( 'buscar' );
    if ( ! $buscar ) return;

    wp_safe_redirect( get_permalink( $buscar ) );
    exit;
} );
