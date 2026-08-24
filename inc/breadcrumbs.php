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
        $post_type_obj = get_post_type_object( 'tramite' );
        $label         = $post_type_obj ? $post_type_obj->labels->name : 'Trámites';

        $home = array_shift( $items );
        array_unshift(
            $items,
            $home,
            [
                'label' => $label,
                'url'   => get_post_type_archive_link( 'tramite' ),
            ]
        );
    }

    return $items;
} );
