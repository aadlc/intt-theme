<?php
if ( ! function_exists( 'get_field' ) ) return;

$post_id = $block->context['postId'] ?? get_the_ID();
if ( ! $post_id ) return;

$titulo    = get_the_title( $post_id );
$municipio     = get_field( 'municipio',            $post_id );
$direccion     = get_field( 'direccion',            $post_id );
$horario       = get_field( 'horario_de_atencion',  $post_id );
$dias          = get_field( 'dias',                 $post_id );
$grupo_mapa    = get_field( 'ubicacion_en_el_mapa', $post_id );
$ubicacion_url = ! empty( $grupo_mapa['url_de_google_maps'] ) ? $grupo_mapa['url_de_google_maps'] : '';
$coordenadas   = ! empty( $grupo_mapa['coordenadas'] )        ? $grupo_mapa['coordenadas']        : '';

$terminos = get_the_terms( $post_id, 'estado' );
$estado   = ( $terminos && ! is_wp_error( $terminos ) ) ? $terminos[0]->name : '';

$mapa_url = '';
if ( $ubicacion_url ) {
    $mapa_url = $ubicacion_url;
} elseif ( $coordenadas ) {
    $partes = array_map( 'trim', explode( ',', $coordenadas, 2 ) );
    if ( 2 === count( $partes ) && is_numeric( $partes[0] ) && is_numeric( $partes[1] ) ) {
        $mapa_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $partes[0] . ',' . $partes[1] );
    }
} elseif ( $direccion ) {
    $mapa_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $direccion );
}
?>
<div class="intt-oficina-card">

	<?php if ( ! is_singular( 'oficina' ) ) : ?>
	<h3 class="wp-block-heading has-heading-4-font-size"><?php echo esc_html( $titulo ); ?></h3>
	<?php endif; ?>

	<?php if ( $estado || $municipio ) : ?>
	<p class="intt-oficina-card__ubicacion"><?php echo esc_html( implode( ', ', array_filter( [ $estado, $municipio ] ) ) ); ?></p>
	<?php endif; ?>

	<?php if ( $direccion ) : ?>
	<p class="intt-oficina-card__direccion"><?php echo esc_html( $direccion ); ?></p>
	<?php endif; ?>

	<?php if ( $horario ) : ?>
	<p class="intt-oficina-card__horario"><?php echo esc_html( 'Horario de atención: ' . $horario ); ?></p>
	<?php endif; ?>

	<?php if ( $dias ) : ?>
	<p class="intt-oficina-card__dias"><?php echo esc_html( 'Días: ' . $dias ); ?></p>
	<?php endif; ?>

	<?php if ( $mapa_url ) : ?>
	<p><a class="intt-oficina-card__mapa" href="<?php echo esc_url( $mapa_url ); ?>" target="_blank" rel="noopener noreferrer">Ver en el mapa</a></p>
	<?php endif; ?>

</div>
