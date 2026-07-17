<?php
$terms = get_terms( [
	'taxonomy'   => 'estado',
	'orderby'    => 'name',
	'order'      => 'ASC',
	'hide_empty' => false,
] );

if ( is_wp_error( $terms ) || empty( $terms ) ) {
	echo '<p class="has-gris-500-color has-text-color">No hay estados registrados.</p>';
	return;
}
?>
<div class="wp-block-group is-layout-grid wp-block-group-is-layout-grid" style="grid-template-columns:1fr;gap:var(--wp--preset--spacing--sp-24)">
<?php foreach ( $terms as $term ) :
	$count = absint( $term->count );
?>
	<article class="wp-block-group intt-tarjeta-tramite">
		<div class="wp-block-group intt-tarjeta-tramite__contenido is-layout-flow wp-block-group-is-layout-flow" style="padding-top:var(--wp--preset--spacing--sp-24);padding-right:var(--wp--preset--spacing--sp-24);padding-bottom:var(--wp--preset--spacing--sp-24);padding-left:var(--wp--preset--spacing--sp-24);--wp--style--block-gap:var(--wp--preset--spacing--sp-8)">
			<h3 class="wp-block-heading has-heading-4-font-size" style="margin-top:0;margin-bottom:0"><a href="<?php echo esc_url( get_term_link( $term ) ); ?>" style="color:var(--wp--preset--color--azul-marino-600);text-decoration:none"><?php echo esc_html( $term->name ); ?></a></h3>
			<p class="has-body-2-font-size" style="margin-top:0;margin-bottom:0"><?php echo esc_html( $count . ' ' . ( $count === 1 ? 'oficina' : 'oficinas' ) ); ?></p>
		</div>
	</article>
<?php endforeach; ?>
</div>
