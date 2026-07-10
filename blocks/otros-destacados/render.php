<?php
// Primera consulta: destacados, ordenados por menu_order.
$destacados = get_posts( [
    'post_type'      => 'tramite',
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'meta_query'     => [
        [
            'key'     => 'destacados_otros_tramites',
            'value'   => '1',
            'compare' => '=',
        ],
    ],
] );

$posts    = $destacados;
$restante = 12 - count( $destacados );

// Segunda consulta: rellena hasta 12 con trámites no destacados, A–Z.
if ( $restante > 0 ) {
    $excluir = wp_list_pluck( $destacados, 'ID' );

    $relleno = get_posts( [
        'post_type'      => 'tramite',
        'post_status'    => 'publish',
        'posts_per_page' => $restante,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post__not_in'   => $excluir,
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'destacados_otros_tramites',
                'value'   => '1',
                'compare' => '!=',
            ],
            [
                'key'     => 'destacados_otros_tramites',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ] );

    $posts = array_merge( $posts, $relleno );
}

if ( empty( $posts ) ) {
    return;
}
?>
<div class="wp-block-group intt-grid-tramites is-layout-grid wp-block-group-is-layout-grid" style="grid-template-columns:repeat(4, minmax(0, 1fr));gap:var(--wp--preset--spacing--sp-24)">

    <?php foreach ( $posts as $post ) :
        $desc = get_field( 'descripcion_corta', $post->ID );
        $url  = get_permalink( $post );
    ?>
    <div class="wp-block-group is-layout-flow wp-block-group-is-layout-flow" style="--wp--style--block-gap:var(--wp--preset--spacing--sp-8)">

        <h3 class="wp-block-heading has-heading-5-font-size" style="margin-top:var(--wp--preset--spacing--sp-0);margin-bottom:var(--wp--preset--spacing--sp-16)">
            <a href="<?php echo esc_url( $url ); ?>" style="font-weight:inherit"><?php echo esc_html( $post->post_title ); ?></a>
        </h3>

        <p class="has-gris-800-color has-text-color" style="margin-top:0;margin-bottom:0"><?php echo esc_html( $desc ); ?></p>

    </div>
    <?php endforeach; ?>

</div>
