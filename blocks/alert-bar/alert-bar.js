( function () {
	var el = document.querySelector( '.intt-alert' );
	if ( ! el ) return;

	if ( ! el.dataset.alertKey ) return;

	var key = 'intt-alert-dismissed-' + el.dataset.alertKey;

	try {
		if ( localStorage.getItem( key ) ) {
			el.hidden = true;
			el.setAttribute( 'aria-hidden', 'true' );
			return;
		}
	} catch ( e ) {}

	var btn = el.querySelector( '.intt-alert__close' );
	if ( ! btn ) return;

	btn.addEventListener( 'click', function () {
		el.hidden = true;
		el.setAttribute( 'aria-hidden', 'true' );
		try {
			localStorage.setItem( key, '1' );
		} catch ( e ) {}
	} );
}() );
