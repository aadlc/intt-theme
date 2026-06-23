<?php
if ( ! function_exists( 'get_field' ) ) return;

$post_id   = $block->context['postId'] ?? get_the_ID();
if ( ! $post_id ) return;

$direccion = get_field( 'direccion',         $post_id );
$jefe      = get_field( '_jefe_de_oficina',  $post_id );
$instagram = get_field( 'instagram',         $post_id );
$x         = get_field( '_x',                $post_id );
$tiktok    = get_field( 'tiktok',            $post_id );
$youtube   = get_field( 'youtube',           $post_id );

$redes = array_filter( [
    'Instagram' => $instagram,
    'X/Twitter' => $x,
    'TikTok'    => $tiktok,
    'YouTube'   => $youtube,
] );
?>
<div class="wp-block-intt-oficina-card">
    <?php if ( $direccion ) : ?>
        <p class="oficina-card__direccion"><?php echo esc_html( $direccion ); ?></p>
    <?php endif; ?>

    <?php if ( $jefe ) : ?>
        <p class="oficina-card__jefe"><?php echo esc_html( $jefe ); ?></p>
    <?php endif; ?>

    <?php if ( $redes ) : ?>
        <ul class="oficina-card__redes">
            <?php foreach ( $redes as $nombre => $url ) : ?>
                <li>
                    <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html( $nombre ); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
