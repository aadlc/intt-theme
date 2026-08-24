<?php
/**
 * Búsqueda global del sitio.
 *
 * Cuando el usuario llega a /?s= con query vacía (por ejemplo desde el
 * ícono de lupa del header), WordPress devuelve la lista de posts como si
 * no hubiera filtro. Este filtro fuerza cero resultados para que se
 * renderice wp:query-no-results de search.html en su lugar.
 */

add_filter( 'posts_pre_query', function ( $posts, $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
        return $posts;
    }
    $s = trim( (string) $query->get( 's' ) );
    if ( '' === $s ) {
        $query->found_posts   = 0;
        $query->max_num_pages = 0;
        return [];
    }
    return $posts;
}, 100, 2 );
