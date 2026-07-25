wp.blocks.registerBlockType( 'intt/cargo-organigrama', {
	edit: function () {
		return wp.element.createElement(
			'p',
			{ className: 'intt-miembro-cargo' },
			'Cargo del miembro'
		);
	},
} );
