( function () {
	var btn   = document.querySelector( '.intt-ver-mas-btn' );
	var panel = document.querySelector( '.intt-otros-panel' );
	if ( ! btn || ! panel ) return;

	btn.addEventListener( 'click', function ( e ) {
		e.preventDefault();
		var isOpen = panel.classList.toggle( 'is-open' );
		btn.textContent = isOpen ? 'Ver menos trámites ↑' : 'Ver más trámites ↓';
		btn.setAttribute( 'aria-expanded', String( isOpen ) );
	} );
} )();
