( function ( blocks, element, serverSideRender ) {
	var el = element.createElement;
	var ServerSideRender = serverSideRender.default || serverSideRender;

	blocks.registerBlockType( 'intt/tipo-tramite-list', {
		edit: function ( props ) {
			return el( ServerSideRender, {
				block: 'intt/tipo-tramite-list',
				attributes: props.attributes,
			} );
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender );
