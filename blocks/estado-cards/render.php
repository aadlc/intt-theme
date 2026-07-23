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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.6.1/dist/css/tom-select.min.css">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.6.1/dist/js/tom-select.complete.min.js"></script>

<div class="intt-oficinas-directorio">

	<div class="intt-oficinas-filtro">
		<label class="intt-oficinas-filtro__label" for="intt-filtro-estado">Filtrar por estado</label>
		<select class="intt-oficinas-filtro__select" id="intt-filtro-estado" placeholder="Selecciona o escribe un estado">
			<option value=""></option>
			<?php foreach ( $terms as $term ) : ?>
			<option value="<?php echo esc_attr( strtolower( $term->name ) ); ?>"><?php echo esc_html( $term->name ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>

	<div class="wp-block-group is-layout-grid wp-block-group-is-layout-grid" style="grid-template-columns:1fr;gap:var(--wp--preset--spacing--sp-24)">
	<?php foreach ( $terms as $term ) :
		$count = absint( $term->count );
	?>
		<article class="wp-block-group intt-tarjeta-tramite" data-estado="<?php echo esc_attr( strtolower( $term->name ) ); ?>">
			<div class="wp-block-group intt-tarjeta-tramite__contenido is-layout-flow wp-block-group-is-layout-flow" style="padding-top:var(--wp--preset--spacing--sp-24);padding-right:var(--wp--preset--spacing--sp-24);padding-bottom:var(--wp--preset--spacing--sp-24);padding-left:var(--wp--preset--spacing--sp-24);--wp--style--block-gap:var(--wp--preset--spacing--sp-8)">
				<h3 class="wp-block-heading has-heading-4-font-size" style="margin-top:0;margin-bottom:0"><a href="<?php echo esc_url( get_term_link( $term ) ); ?>" style="color:var(--wp--preset--color--azul-marino-600);text-decoration:none"><?php echo esc_html( $term->name ); ?></a></h3>
				<p class="has-body-2-font-size" style="margin-top:0;margin-bottom:0"><?php echo esc_html( $count . ' ' . ( $count === 1 ? 'oficina' : 'oficinas' ) ); ?></p>
			</div>
		</article>
	<?php endforeach; ?>
	</div>

	<p class="intt-oficinas-sin-resultados" style="display:none">No se encontraron oficinas para ese estado.</p>

</div>

<script>
( function () {
	var cards         = document.querySelectorAll( '.intt-tarjeta-tramite[data-estado]' );
	var sinResultados = document.querySelector( '.intt-oficinas-sin-resultados' );

	function filtrar( value ) {
		var q        = ( value || '' ).trim();
		var visibles = 0;
		cards.forEach( function ( card ) {
			var oculta = q && card.dataset.estado !== q;
			card.style.display = oculta ? 'none' : '';
			if ( ! oculta ) visibles++;
		} );
		if ( sinResultados ) {
			sinResultados.style.display = ( q && visibles === 0 ) ? '' : 'none';
		}
	}

	function init() {
		if ( typeof TomSelect === 'undefined' ) return;
		new TomSelect( '#intt-filtro-estado', {
			create:           false,
			placeholder:      'Selecciona o escribe un estado',
			allowEmptyOption: false,
			maxOptions:       null,
			plugins:          [ 'clear_button' ],
			onChange:         filtrar,
			render: {
				no_results: function () {
					return '<div class="no-results">No se encontraron resultados</div>';
				},
			},
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
</script>
