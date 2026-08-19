<?php
/**
 * Overrides puntuales de traducciones para strings de WP core que aún no
 * tienen traducción en el paquete es_VE.
 *
 * Estrategia: filtro sobre `gettext` con dominio 'default' (WP core).
 * Retorno temprano si el dominio no es core → impacto de perf mínimo.
 *
 * Cuando WP suba la traducción faltante al paquete es_VE, el filtro se
 * vuelve inofensivo (retorna el string sin modificar) y puede eliminarse.
 *
 * Referencia: es un problema conocido y ampliamente documentado.
 * Ver hilo canónico en wordpress.org/support/topic/search-results-for-translate-title/
 */

add_filter( 'gettext', function ( $translation, $text, $domain ) {

    if ( 'default' !== $domain ) {
        return $translation;
    }

    // Título del archivo de búsqueda: get_the_archive_title() usa esta
    // variante (con comillas curly codificadas) que no está traducida en
    // es_VE al 2026-08-19. La variante sin comillas curly sí lo está.
    if ( 'Search results for: &#8220;%s&#8221;' === $text ) {
        return 'Resultados de búsqueda para: &#8220;%s&#8221;';
    }

    return $translation;
}, 10, 3 );
