<?php
/**
 * Replaces {year} placeholder in the footer template part with the current year.
 */
add_filter( 'render_block_core/template-part', function ( $content, $block ) {

    if ( 'footer' !== ( $block['attrs']['slug'] ?? '' ) ) {
        return $content;
    }

    return str_replace( '{year}', date( 'Y' ), $content );

}, 10, 2 );
