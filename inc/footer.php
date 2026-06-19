<?php
/**
 * Reemplaza {year} con el año actual en el pie de página.
 */
add_filter( 'render_block_core/template-part', function ( $content, $block ) {

    if ( 'footer' !== ( $block['attrs']['slug'] ?? '' ) ) {
        return $content;
    }

    return str_replace( '{year}', wp_date( 'Y' ), $content );

}, 10, 2 );
