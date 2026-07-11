<?php
/**
 * Trámites — CPT, Taxonomías y utilidades
 */

// ── Registro ──────────────────────────────────────────────────────────────────

add_action( 'init', 'intt_registrar_cpt_tramites' );

function intt_registrar_cpt_tramites() {

    add_rewrite_tag( '%tipo_tramite%', '([^/]+)', 'tipo_tramite=' );
}

// ── Rewrite slug: ACF no soporta tokens en el slug, se corrige aquí ──────────
// ACF registra el CPT con slug plano "tramites". Este filtro lo reemplaza por
// "tramites/%tipo_tramite%" antes de que WordPress procese el rewrite, evitando
// la colisión de reglas con el archivo de taxonomía /tramites/licencias/.

add_filter( 'register_post_type_args', function ( $args, $post_type ) {
    if ( $post_type === 'tramite' ) {
        $args['rewrite'] = [ 'slug' => 'tramites/%tipo_tramite%', 'with_front' => false ];
    }
    return $args;
}, 10, 2 );

// ── Flush en activación del tema ──────────────────────────────────────────────

add_action( 'after_switch_theme', 'intt_flush_rewrite_rules' );

function intt_flush_rewrite_rules() {
    flush_rewrite_rules();
}

// ── Descripción corta ─────────────────────────────────────────────────────────
// El campo descripcion_corta lo gestiona ACF Pro (grupo "Datos del Trámite").
// Solo se registra el meta para que esté disponible en la REST API y en
// el filtro get_the_excerpt.

add_action( 'init', 'intt_registrar_meta_descripcion_corta' );

function intt_registrar_meta_descripcion_corta() {
    register_post_meta( 'tramite', 'descripcion_corta', [
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() { return current_user_can( 'edit_posts' ); },
    ] );
}

// ── Quick Edit ────────────────────────────────────────────────────────────────

add_filter( 'manage_tramite_posts_columns', 'intt_columnas_tramite' );

function intt_columnas_tramite( $columns ) {
    $columns['intt_desc_corta'] = 'Descripción corta';
    return $columns;
}

add_action( 'manage_tramite_posts_custom_column', 'intt_contenido_columna_tramite', 10, 2 );

function intt_contenido_columna_tramite( $column, $post_id ) {
    if ( $column !== 'intt_desc_corta' ) return;
    echo '<span class="intt-qe-desc">' . esc_html( get_post_meta( $post_id, 'descripcion_corta', true ) ) . '</span>';
}

add_action( 'admin_head', 'intt_ocultar_columna_desc_corta' );

function intt_ocultar_columna_desc_corta() {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'tramite' ) return;
    // echo '<style>.column-intt_desc_corta { display:none; }</style>';
}

// ── Excerpt → descripcion_corta ───────────────────────────────────────────────
// Hace que wp:post-excerpt muestre descripcion_corta en trámites,
// sin modificar el template ni el campo excerpt de la BD.

add_filter( 'get_the_excerpt', function ( $excerpt, $post = null ) {
    $post = $post ?: get_post();
    if ( ! $post || get_post_type( $post ) !== 'tramite' ) return $excerpt;
    $desc = get_field( 'descripcion_corta', $post->ID );
    if ( ! empty( $desc ) ) return $desc;
    return $excerpt;
}, 10, 2 );

// ── Orden A-Z en el archivo del CPT y en páginas de taxonomía ────────────────

add_action( 'pre_get_posts', 'intt_ordenar_tramites_az' );

function intt_ordenar_tramites_az( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( ! $query->is_post_type_archive( 'tramite' ) && ! $query->is_tax( 'tipo_tramite' ) ) return;

    $query->set( 'orderby', 'title' );
    $query->set( 'order', 'ASC' );
    $query->set( 'posts_per_page', 100 );
}

// ── Permalink: resolver el marcador %tipo_tramite% ────────────────────────────

add_filter( 'post_type_link', 'intt_resolver_permalink_tramite', 10, 2 );

function intt_resolver_permalink_tramite( $url, $post ) {
    if ( $post->post_type !== 'tramite' ) return $url;
    if ( strpos( $url, '%tipo_tramite%' ) === false ) return $url;

    $terms = get_the_terms( $post, 'tipo_tramite' );
    if ( ! $terms || is_wp_error( $terms ) ) {
        return str_replace( '%tipo_tramite%', 'sin-categoria', $url );
    }

    // Ordenar por term_id ASC para resultado predecible cuando hay varios términos
    $terms_sorted = wp_list_sort( $terms, [ 'term_id' => 'ASC' ] );
    $slug         = reset( $terms_sorted )->slug;

    return str_replace( '%tipo_tramite%', $slug, $url );
}

