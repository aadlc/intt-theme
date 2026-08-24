<?php
/**
 * Páginas por defecto creadas automáticamente por el tema.
 *
 * Se crean solo si no existen, tanto al activar el tema como en cada carga
 * de admin (por si alguien las eliminó y hay que recrearlas). El contenido
 * inicial es vacío — el template específico (page-{slug}.html) define el
 * layout con bloques bloqueados.
 */

add_action( 'after_switch_theme', 'intt_crear_paginas_por_defecto' );
add_action( 'admin_init',         'intt_crear_paginas_por_defecto' );

function intt_crear_paginas_por_defecto() {
    $paginas = [
        'buscar' => 'Buscar',
    ];

    foreach ( $paginas as $slug => $titulo ) {
        if ( get_page_by_path( $slug ) ) continue;

        wp_insert_post( [
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'post_title'     => $titulo,
            'post_name'      => $slug,
            'post_content'   => '',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        ] );
    }
}
