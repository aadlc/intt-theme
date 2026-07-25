<?php
if ( ! function_exists( 'get_field' ) ) return;

$post_id = $block->context['postId'] ?? get_the_ID();
if ( ! $post_id ) return;

$cargo = get_field( 'cargo', $post_id );
if ( ! $cargo ) return;

echo '<p class="intt-miembro-cargo">' . esc_html( $cargo ) . '</p>';
