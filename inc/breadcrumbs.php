<?php
/**
 * Personalización del bloque nativo wp:breadcrumbs.
 *
 * WP no incluye el archivo del CPT en el trail cuando se está en una página
 * de taxonomía asociada a ese CPT (limitación conocida, ver GitHub #72498).
 * El filtro block_core_breadcrumbs_items (WP 7.0+) permite inyectar items
 * en el trail antes de renderizarlo.
 */

add_filter( 'block_core_breadcrumbs_items', function ( $items ) {

    if ( is_tax( 'tipo_tramite' ) ) {
        $home = array_shift( $items );
        array_unshift(
            $items,
            $home,
            [
                'label' => 'Trámites',
                'url'   => get_post_type_archive_link( 'tramite' ),
            ]
        );
    }

    return $items;
} );
