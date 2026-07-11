( function ( blocks, element, serverSideRender ) {
	var el = element.createElement;
	var ServerSideRender = serverSideRender.default || serverSideRender;

	blocks.registerBlockType( 'intt/tramite-descripcion', {
		edit: function ( props ) {
			return el( ServerSideRender, {
				block: 'intt/tramite-descripcion',
				attributes: props.attributes,
			} );
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender );
