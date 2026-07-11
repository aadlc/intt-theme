<?php
$post_id = $block->context['postId'] ?? get_the_ID();
$desc    = get_field( 'descripcion_corta', $post_id );

if ( empty( $desc ) ) {
    return;
}
?>
<p class="has-heading-4-font-size has-gris-800-color has-text-color" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--sp-32)"><?php echo esc_html( $desc ); ?></p>
