<?php
if ( ! function_exists( 'get_field' ) ) return;

$post_id = $block->context['postId'] ?? get_the_ID();
if ( ! $post_id ) return;

$titulo    = get_the_title( $post_id );
$municipio = get_field( 'municipio',          $post_id );
$direccion = get_field( 'direccion',          $post_id );
$horario   = get_field( 'horario_de_operacion', $post_id );
$mapa      = get_field( 'ubicacion_mapa',     $post_id );

$terminos = get_the_terms( $post_id, 'estado' );
$estado   = ( $terminos && ! is_wp_error( $terminos ) ) ? $terminos[0]->name : '';

$mapa_url = '';
if ( $mapa ) {
    if ( ! empty( $mapa['lat'] ) && ! empty( $mapa['lng'] ) ) {
        $mapa_url = 'https://www.google.com/maps?q=' . rawurlencode( $mapa['lat'] . ',' . $mapa['lng'] );
    } elseif ( ! empty( $mapa['address'] ) ) {
        $mapa_url = 'https://www.google.com/maps/search/' . rawurlencode( $mapa['address'] );
    }
}
?>
<div class="intt-oficina-card">

	<h3 class="wp-block-heading has-heading-4-font-size"><?php echo esc_html( $titulo ); ?></h3>

	<?php if ( $estado || $municipio ) : ?>
	<p class="intt-oficina-card__ubicacion"><?php echo esc_html( implode( ', ', array_filter( [ $estado, $municipio ] ) ) ); ?></p>
	<?php endif; ?>

	<?php if ( $direccion ) : ?>
	<p class="intt-oficina-card__direccion"><?php echo esc_html( $direccion ); ?></p>
	<?php endif; ?>

	<?php if ( $horario ) : ?>
	<p class="intt-oficina-card__horario"><?php echo nl2br( esc_html( $horario ) ); ?></p>
	<?php endif; ?>

	<?php if ( $mapa_url ) : ?>
	<a class="intt-oficina-card__mapa" href="<?php echo esc_url( $mapa_url ); ?>" target="_blank" rel="noopener noreferrer">Ver en el mapa</a>
	<?php endif; ?>

</div>
