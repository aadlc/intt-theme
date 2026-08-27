<?php
/**
 * Oficinas — filtros de excerpt para búsqueda y listados.
 *
 * Las oficinas se registran vía ACF (post type "oficina") y sus datos viven
 * en campos ACF (municipio, direccion, estado), no en post_content. Sin estos
 * filtros, wp:post-excerpt devuelve cadena vacía en oficinas.
 */

// Genera el texto que se usa como excerpt de una oficina.
// Formato: "Municipio, Estado · Dirección".
function intt_oficina_excerpt_source( $post_id ) {
    if ( ! function_exists( 'get_field' ) ) return '';

    $municipio = get_field( 'municipio', $post_id );
    $direccion = get_field( 'direccion', $post_id );
    $terminos  = get_the_terms( $post_id, 'estado' );
    $estado    = ( $terminos && ! is_wp_error( $terminos ) ) ? $terminos[0]->name : '';

    $lugar = implode( ', ', array_filter( [ $municipio, $estado ] ) );
    return implode( ' · ', array_filter( [ $lugar, $direccion ] ) );
}

// Los dos filtros que siguen operan en contextos distintos y por eso no se
// pueden centralizar:
//   - `get_the_excerpt` es la API pública de WP: la llaman los bloques
//     (wp:post-excerpt) para decidir qué texto renderizar en el markup final.
//   - `relevanssi_excerpt_content` es interno de Relevanssi: le dice al plugin
//     qué contenido usar como fuente al construir su snippet resaltado
//     (Relevanssi normalmente lee post_content, pero en oficinas está vacío
//     porque los datos viven en ACF).

// wp:post-excerpt muestra la línea de ubicación fuera de búsqueda.
// En búsqueda delega a Relevanssi para snippet con highlighting.
add_filter( 'get_the_excerpt', function ( $excerpt, $post = null ) {
    $post = $post ?: get_post();
    if ( ! $post || get_post_type( $post ) !== 'oficina' ) return $excerpt;

    if ( is_search() && function_exists( 'relevanssi_do_excerpt' ) ) {
        $query = trim( get_search_query() );
        if ( '' !== $query ) return relevanssi_do_excerpt( $post, $query );
    }

    $source = intt_oficina_excerpt_source( $post->ID );
    return $source ?: $excerpt;
}, 10, 2 );

// Relevanssi construye su snippet con la línea de ubicación en lugar del
// post_content (vacío en oficinas: datos en ACF).
add_filter( 'relevanssi_excerpt_content', function ( $content, $post ) {
    if ( ! $post || get_post_type( $post ) !== 'oficina' ) return $content;
    $source = intt_oficina_excerpt_source( $post->ID );
    return $source ?: $content;
}, 10, 2 );
