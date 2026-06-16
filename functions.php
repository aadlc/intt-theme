<?php
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style( 'intt-style', get_stylesheet_uri(), [], filemtime( get_template_directory() . '/style.css' ) );
} );

add_action( 'after_setup_theme', function () {
    add_editor_style( 'style.css' );
} );

add_action( 'init', function () {
    register_block_pattern_category( 'intt-tramites',    [ 'label' => 'INTT — Trámites' ] );
    register_block_pattern_category( 'intt-componentes', [ 'label' => 'INTT — Componentes' ] );
}, 1 );

require_once get_template_directory() . '/inc/cpt-tramites.php';
require_once get_template_directory() . '/inc/alert-bar.php';
require_once get_template_directory() . '/inc/footer.php';
require_once get_template_directory() . '/inc/default-pages.php';

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook !== 'edit.php' ) return;
    if ( get_current_screen()->post_type !== 'tramite' ) return;
    wp_enqueue_script(
        'intt-tramite-quick-edit',
        get_template_directory_uri() . '/assets/js/tramite-quick-edit.js',
        [ 'jquery', 'inline-edit-post' ],
        filemtime( get_template_directory() . '/assets/js/tramite-quick-edit.js' ),
        true
    );
} );

add_action( 'init', function () {
    register_block_type( get_template_directory() . '/blocks/alert-bar' );
    register_block_type( get_template_directory() . '/blocks/hub-list' );
    register_block_type( get_template_directory() . '/blocks/hub-sidebar' );
    register_block_type( get_template_directory() . '/blocks/tramite-descripcion' );
}, 5 );
