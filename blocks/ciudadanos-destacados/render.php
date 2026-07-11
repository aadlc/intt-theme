<?php
$posts = get_posts( [
    'post_type'      => 'tramite',
    'post_status'    => 'publish',
    'posts_per_page' => 4,
    'meta_query'     => [
        [
            'key'     => 'destacados_ciudadanos',
            'value'   => '1',
            'compare' => '=',
        ],
    ],
] );

if ( empty( $posts ) ) {
    return;
}
?>
<div class="wp-block-group is-layout-flow wp-block-group-is-layout-flow" style="--wp--style--block-gap:var(--wp--preset--spacing--sp-16)">

    <p style="text-transform:uppercase;margin-top:0;margin-bottom:0">Ciudadanos</p>

    <div class="wp-block-group intt-grid-tramites is-layout-grid wp-block-group-is-layout-grid" style="grid-template-columns:repeat(4, minmax(0, 1fr));gap:var(--wp--preset--spacing--sp-24)">

        <?php foreach ( $posts as $post ) :
            $imagen = get_field( 'imagen_destacada', $post->ID );
            $desc   = get_field( 'descripcion_corta', $post->ID );
            $url    = get_permalink( $post );
        ?>
        <article class="wp-block-group intt-tarjeta-tramite">

            <figure class="wp-block-image intt-tarjeta-tramite__imagen" style="margin:0">
                <?php if ( $imagen ) : ?>
                <img src="<?php echo esc_url( $imagen ); ?>" alt="<?php echo esc_attr( $post->post_title ); ?>"/>
                <?php endif; ?>
            </figure>

            <div class="wp-block-group intt-tarjeta-tramite__contenido is-layout-flow wp-block-group-is-layout-flow" style="padding-top:var(--wp--preset--spacing--sp-24);padding-right:var(--wp--preset--spacing--sp-24);padding-bottom:var(--wp--preset--spacing--sp-24);padding-left:var(--wp--preset--spacing--sp-24);--wp--style--block-gap:var(--wp--preset--spacing--sp-8)">
                <h3 class="wp-block-heading has-heading-5-font-size"><?php echo esc_html( $post->post_title ); ?></h3>
                <p class="has-body-2-font-size"><?php echo esc_html( $desc ); ?></p>
            </div>

            <div class="wp-block-group intt-tarjeta-tramite__cta is-layout-flex is-nowrap wp-block-group-is-layout-flex" style="padding-top:var(--wp--preset--spacing--sp-0);padding-right:var(--wp--preset--spacing--sp-24);padding-bottom:var(--wp--preset--spacing--sp-16);padding-left:var(--wp--preset--spacing--sp-24)">
                <p><a href="<?php echo esc_url( $url ); ?>">Ver trámite →</a></p>
            </div>

        </article>
        <?php endforeach; ?>

    </div>
</div>
