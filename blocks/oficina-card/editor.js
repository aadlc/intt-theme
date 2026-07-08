( function ( blocks, element, serverSideRender, components ) {
	var el = element.createElement;
	var ServerSideRender = serverSideRender.default || serverSideRender;
	var Placeholder = components.Placeholder;

	blocks.registerBlockType( 'intt/oficina-card', {
		edit: function ( props ) {
			var context = props.context || {};
			var postId = context.postId;

			if ( postId ) {
				return el( ServerSideRender, {
					block: 'intt/oficina-card',
					attributes: props.attributes,
					urlQueryArgs: { post_id: postId },
				} );
			}

			return el( Placeholder, {
				icon: 'building',
				label: 'Datos de la Oficina',
				instructions: 'Vista previa disponible dentro del loop de entradas.',
			} );
		},
		save: function () {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.components );
