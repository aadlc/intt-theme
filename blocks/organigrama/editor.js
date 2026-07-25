wp.blocks.registerBlockType( 'intt/organigrama', {
	edit: function () {
		return wp.element.createElement(
			'p',
			{ style: { padding: '1em', border: '1px dashed #ccc', color: '#888' } },
			'Organigrama Institucional — se renderiza en el frontend'
		);
	},
} );
