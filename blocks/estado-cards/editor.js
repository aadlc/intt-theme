( function ( blocks, element, serverSideRender ) {
	var el = element.createElement;
	var ServerSideRender = serverSideRender.default || serverSideRender;

	blocks.registerBlockType( 'intt/estado-cards', {
		edit: function ( props ) {
			return el( ServerSideRender, {
				block: 'intt/estado-cards',
				attributes: props.attributes,
			} );
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender );
