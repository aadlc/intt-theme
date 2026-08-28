<?php
/**
 * Páginas por defecto creadas automáticamente por el tema.
 *
 * Se crean solo si no existen, tanto al activar el tema como en cada carga
 * de admin (por si alguien las eliminó y hay que recrearlas). El contenido
 * inicial de cada página se define aquí — si el editor lo modifica después,
 * el cambio se respeta (get_page_by_path devuelve la página y se salta).
 */

add_action( 'after_switch_theme', 'intt_crear_paginas_por_defecto' );
add_action( 'admin_init',         'intt_crear_paginas_por_defecto' );

function intt_crear_paginas_por_defecto() {
    $paginas = [
        'buscar' => [
            'titulo'    => 'Buscar',
            'contenido' => '',
        ],
        'biblioteca' => [
            'titulo'    => 'Biblioteca',
            'contenido' => intt_biblioteca_contenido_inicial(),
        ],
    ];

    foreach ( $paginas as $slug => $config ) {
        if ( get_page_by_path( $slug ) ) continue;

        wp_insert_post( [
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'post_title'     => $config['titulo'],
            'post_name'      => $slug,
            'post_content'   => $config['contenido'],
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        ] );
    }
}

function intt_biblioteca_contenido_inicial(): string {
    return <<<'HTML'
<!-- wp:paragraph {"className":"has-heading-4-font-size"} -->
<p class="has-heading-4-font-size">Cultura del Transporte · Educación para la Prevención y Seguridad Vial</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Consulta y descarga documentos técnicos y legales sobre transporte terrestre, educación y seguridad vial. Libre acceso para estudiantes, profesionales del sector y ciudadanos.</p>
<!-- /wp:paragraph -->
HTML;
}
