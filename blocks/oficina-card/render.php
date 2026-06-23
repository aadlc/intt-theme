<?php
if ( ! function_exists( 'get_field' ) ) return;

$post_id = $block->context['postId'] ?? get_the_ID();
if ( ! $post_id ) return;

$title     = get_the_title( $post_id );
$direccion = get_field( 'direccion',        $post_id );
$jefe      = get_field( '_jefe_de_oficina', $post_id );
$instagram = get_field( 'instagram',        $post_id );
$tiktok    = get_field( 'tiktok',           $post_id );
$youtube   = get_field( 'youtube',          $post_id );

$x_raw = get_field( '_x', $post_id );
$x_url = is_array( $x_raw ) ? ( $x_raw['url'] ?? '' ) : (string) $x_raw;

$social_items = '';
if ( $x_url )     $social_items .= '<!-- wp:social-link {"url":"' . esc_url( $x_url )     . '","service":"x"} /-->';
if ( $instagram ) $social_items .= '<!-- wp:social-link {"url":"' . esc_url( $instagram ) . '","service":"instagram"} /-->';
if ( $tiktok )    $social_items .= '<!-- wp:social-link {"url":"' . esc_url( $tiktok )    . '","service":"tiktok"} /-->';
if ( $youtube )   $social_items .= '<!-- wp:social-link {"url":"' . esc_url( $youtube )   . '","service":"youtube"} /-->';
?>
<div class="intt-oficina-card">

	<h3 class="wp-block-heading has-heading-4-font-size"><?php echo esc_html( $title ); ?></h3>

	<?php if ( $direccion ) : ?>
	<p><?php echo esc_html( $direccion ); ?></p>
	<?php endif; ?>

	<?php if ( $jefe ) : ?>
	<p>Jefe de oficina: <?php echo esc_html( $jefe ); ?></p>
	<?php endif; ?>

	<?php if ( $social_items ) :
		echo do_blocks(
			'<!-- wp:social-links {"iconBackgroundColor":"gris-600","iconBackgroundColorValue":"#757575","size":"has-small-icon-size"} -->' .
			'<ul class="wp-block-social-links has-small-icon-size has-icon-background-color">' .
			$social_items .
			'</ul><!-- /wp:social-links -->'
		);
	endif; ?>

</div>
