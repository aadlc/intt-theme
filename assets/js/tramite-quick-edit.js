jQuery( function ( $ ) {
	var $wp_inline_edit = inlineEditPost.edit;

	inlineEditPost.edit = function ( id ) {
		$wp_inline_edit.apply( this, arguments );

		var postId = typeof id === 'object' ? parseInt( this.getId( id ) ) : id;
		var desc   = $( '#post-' + postId ).find( '.intt-qe-desc' ).text();

		$( '#edit-' + postId ).find( 'textarea[name="descripcion_corta"]' ).val( desc );
	};
} );
