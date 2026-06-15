<?php
/**
 * Trámites — CPT, Taxonomías y utilidades
 */

// ── Registro ──────────────────────────────────────────────────────────────────

add_action( 'init', 'intt_registrar_cpt_tramites' );

function intt_registrar_cpt_tramites() {

    add_rewrite_tag( '%tipo_tramite%', '([^/]+)', 'tipo_tramite=' );

    register_post_type( 'tramite', [
        'labels' => [
            'name'               => 'Trámites',
            'singular_name'      => 'Trámite',
            'add_new_item'       => 'Agregar trámite',
            'edit_item'          => 'Editar trámite',
            'view_item'          => 'Ver trámite',
            'all_items'          => 'Todos los trámites',
            'archives'           => 'Trámites',
            'search_items'       => 'Buscar trámites',
            'not_found'          => 'No se encontraron trámites.',
            'not_found_in_trash' => 'No hay trámites en la papelera.',
        ],
        'public'        => true,
        'has_archive'   => 'tramites',
        'supports'      => [ 'title', 'editor', 'thumbnail', 'page-attributes' ],
        'menu_icon'     => 'dashicons-clipboard',
        'rewrite'       => [ 'slug' => 'tramites/%tipo_tramite%', 'with_front' => false ],
        'show_in_rest'  => true,
        'menu_position' => 5,
    ] );

    // Taxonomía jerárquica que impulsa las URLs de los hubs (/tramites/licencias/, etc.)
    register_taxonomy( 'tipo_tramite', 'tramite', [
        'labels' => [
            'name'          => 'Tipos de trámite',
            'singular_name' => 'Tipo de trámite',
            'all_items'     => 'Todos los tipos',
            'add_new_item'  => 'Agregar tipo',
            'edit_item'     => 'Editar tipo',
        ],
        'hierarchical' => true,
        'public'       => true,
        'show_in_rest' => true,
        'rewrite'      => [ 'slug' => 'tramites', 'with_front' => false ],
    ] );
}

// ── Flush en activación del tema ──────────────────────────────────────────────

add_action( 'after_switch_theme', 'intt_flush_rewrite_rules' );

function intt_flush_rewrite_rules() {
    flush_rewrite_rules();
}

// ── Descripción corta ─────────────────────────────────────────────────────────

add_action( 'init', 'intt_registrar_meta_descripcion_corta' );

function intt_registrar_meta_descripcion_corta() {
    register_post_meta( 'tramite', 'descripcion_corta', [
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => 'intt_meta_auth_callback',
    ] );
}

function intt_meta_auth_callback() {
    return current_user_can( 'edit_posts' );
}

add_action( 'add_meta_boxes', 'intt_agregar_meta_box_descripcion_corta' );

function intt_agregar_meta_box_descripcion_corta() {
    add_meta_box(
        'intt_descripcion_corta',
        'Descripción corta',
        'intt_render_meta_box_descripcion_corta',
        'tramite',
        'side',
        'high'
    );
}

function intt_render_meta_box_descripcion_corta( $post ) {
    wp_nonce_field( 'intt_descripcion_corta', 'intt_descripcion_corta_nonce' );
    $value = get_post_meta( $post->ID, 'descripcion_corta', true );
    ?>
    <textarea name="descripcion_corta" rows="3" style="width:100%;resize:vertical"><?php echo esc_textarea( $value ); ?></textarea>
    <p class="description">Se muestra bajo el título en la página del hub.</p>
    <?php
}

add_action( 'save_post_tramite', 'intt_guardar_descripcion_corta' );

function intt_guardar_descripcion_corta( $post_id ) {
    if ( wp_is_post_autosave( $post_id ) ) return;
    if ( wp_is_post_revision( $post_id ) ) return;
    if ( ! isset( $_POST['descripcion_corta'] ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Meta box: verificar nonce propio. Quick Edit: confiar en el nonce de WP.
    if ( isset( $_POST['intt_descripcion_corta_nonce'] ) ) {
        if ( ! wp_verify_nonce( $_POST['intt_descripcion_corta_nonce'], 'intt_descripcion_corta' ) ) return;
    }

    update_post_meta(
        $post_id,
        'descripcion_corta',
        sanitize_text_field( wp_unslash( $_POST['descripcion_corta'] ) )
    );
}

// ── Quick Edit ────────────────────────────────────────────────────────────────

add_filter( 'manage_tramite_posts_columns', 'intt_columnas_tramite' );

function intt_columnas_tramite( $columns ) {
    $columns['intt_desc_corta'] = '';
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
    echo '<style>.column-intt_desc_corta { display:none; }</style>';
}

add_action( 'quick_edit_custom_box', 'intt_quick_edit_descripcion_corta', 10, 2 );

function intt_quick_edit_descripcion_corta( $column, $post_type ) {
    if ( $column !== 'intt_desc_corta' || $post_type !== 'tramite' ) return;
    ?>
    <fieldset class="inline-edit-col-left" style="width:100%">
        <div class="inline-edit-col">
            <label style="display:block">
                <span class="title">Descripción</span>
                <textarea name="descripcion_corta" rows="2" style="width:100%"></textarea>
            </label>
        </div>
    </fieldset>
    <?php
}

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
