<?php
/**
 * Organigrama — Meta, utilidades y siembra de términos
 * El CPT "organigrama" y la taxonomía "nivel_organigrama" los registra ACF.
 */

// ── Placeholder del campo título ──────────────────────────────────────────

add_filter( 'enter_title_here', function( $placeholder, $post ) {
    if ( get_post_type( $post ) === 'organigrama' ) {
        return 'Nombre completo';
    }
    return $placeholder;
}, 10, 2 );

// ── Meta: cargo ───────────────────────────────────────────────────────────
// ACF gestiona el campo en el editor. register_post_meta lo expone en REST
// para que wp:post-meta pueda leerlo dentro del Query Loop.

add_action( 'init', 'intt_registrar_meta_cargo_organigrama' );

function intt_registrar_meta_cargo_organigrama() {
    register_post_meta( 'organigrama', 'cargo', [
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() { return current_user_can( 'edit_posts' ); },
    ] );
}

// ── Siembra de términos en activación del tema ────────────────────────────
// Para sembrar manualmente: wp eval 'intt_sembrar_terminos_nivel_organigrama();'

add_action( 'after_switch_theme', 'intt_sembrar_terminos_nivel_organigrama' );

function intt_sembrar_terminos_nivel_organigrama() {
    $terminos = [
        'estrategico'    => 'Nivel Estratégico',
        'asesoria-apoyo' => 'Nivel de Asesoría y Apoyo',
        'sustantivo'     => 'Nivel Sustantivo',
    ];

    foreach ( $terminos as $slug => $nombre ) {
        if ( ! term_exists( $slug, 'nivel_organigrama' ) ) {
            wp_insert_term( $nombre, 'nivel_organigrama', [ 'slug' => $slug ] );
        }
    }
}
