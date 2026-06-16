( function ( blocks, element, components ) {
	blocks.registerBlockType( 'intt/alert-bar', {
		edit: function () {
			return element.createElement(
				components.Placeholder,
				{
					icon: 'warning',
					label: 'Barra de Alerta — INTT',
					instructions: 'Muestra la alerta activa del sitio. Gestionar en WP Admin → Alertas.'
				}
			);
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.components );
