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

	if ( typeof TomSelect === 'undefined' ) return;

	new TomSelect( '#intt-filtro-estado', {
		create:           false,
		placeholder:      'Selecciona o escribe un estado',
		allowEmptyOption: false,
		maxOptions:       null,
		plugins:          [ 'clear_button' ],
		onChange:         filtrar,
		onInitialize: function () {
			var label = document.querySelector( 'label[for="intt-filtro-estado"]' );
			if ( label ) label.setAttribute( 'for', this.control_input.id );
		},
		render: {
			no_results: function () {
				return '<div class="no-results">No se encontraron resultados</div>';
			},
		},
	} );
} )();
