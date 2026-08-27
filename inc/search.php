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

// Relevanssi Live Ajax Search: por default inyecta el div de resultados
// dentro del form. Nuestro <main> con .wp-block-group-is-layout-constrained
// tiene position:relative (regla global de WP), lo que rompe el positioning
// absoluto del div y hace caer los resultados al footer. Este filtro delega
// el div al <body>, donde el positioning del plugin funciona como espera.
// Ver https://www.relevanssi.com/live-ajax-search/#astra
add_filter( 'relevanssi_live_search_add_result_div', '__return_false' );

// Relevanssi Live Ajax Search: traducción de los strings del dropdown.
//
// El sitio está en `es_VE` pero el plugin no tiene traducción `es_VE`
// disponible en translate.wordpress.org (solo `es_ES` y `es_CL`), y WP no
// hace fallback automático entre variantes de español. Usamos el filtro
// `gettext`/`ngettext` como mecanismo oficial para sobrescribir los strings
// sin depender del sistema global de traducciones.
//
// Cuando `es_VE` esté disponible oficialmente (ver "Cómo contribuir" abajo)
// e instalada con `wp language plugin install relevanssi-live-ajax-search es_VE`,
// se puede remover este bloque completo.
//
// Cómo contribuir la traducción `es_VE` al proyecto:
//   1. Loguearse en https://login.wordpress.org/
//   2. Ir a https://translate.wordpress.org/projects/wp-plugins/relevanssi-live-ajax-search/
//   3. Seleccionar "Spanish (Venezuela)" y proponer las mismas cadenas de abajo.
add_filter( 'gettext', function ( $translation, $text, $domain ) {
    if ( 'relevanssi-live-ajax-search' !== $domain ) return $translation;

    if ( 'Press enter to see all the results.' === $text ) {
        return 'Presiona Enter para ver todos los resultados.';
    }

    return $translation;
}, 10, 3 );

add_filter( 'ngettext', function ( $translation, $single, $plural, $number, $domain ) {
    if ( 'relevanssi-live-ajax-search' !== $domain ) return $translation;

    if ( '%d result found.' === $single && '%d results found.' === $plural ) {
        return 1 === $number
            ? '%d resultado encontrado.'
            : '%d resultados encontrados.';
    }

    return $translation;
}, 10, 5 );
