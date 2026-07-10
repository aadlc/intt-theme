( function ( blocks, element, serverSideRender ) {
	var el = element.createElement;
	var ServerSideRender = serverSideRender.default || serverSideRender;

	blocks.registerBlockType( 'intt/empresas-destacados', {
		edit: function ( props ) {
			return el( ServerSideRender, {
				block: 'intt/empresas-destacados',
				attributes: props.attributes,
			} );
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender );
