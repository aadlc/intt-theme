( function () {
	var el = document.querySelector( '.intt-alert' );
	if ( ! el ) return;

	var key = 'intt-alert-dismissed-' + el.dataset.alertKey;

	if ( localStorage.getItem( key ) ) {
		el.hidden = true;
		return;
	}

	var btn = el.querySelector( '.intt-alert__close' );
	if ( ! btn ) return;

	btn.addEventListener( 'click', function () {
		localStorage.setItem( key, '1' );
		el.hidden = true;
	} );
}() );
