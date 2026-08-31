/**
 * Front-end behaviour for the Event Agenda V2 block: clicking a day button
 * switches which day's agenda is shown.
 */

( function () {
	function init() {
		document.querySelectorAll( '.event-agenda-v2-day-button' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				const section = button.closest( '.event-agenda-v2-section' );

				if ( ! section ) {
					return;
				}

				section.querySelectorAll( '.event-agenda-v2-day-button' ).forEach( function ( otherButton ) {
					otherButton.classList.remove( 'active' );
					otherButton.setAttribute( 'aria-selected', 'false' );
				} );
				button.classList.add( 'active' );
				button.setAttribute( 'aria-selected', 'true' );

				section.querySelectorAll( '.event-agenda-v2-day' ).forEach( function ( day ) {
					day.style.display = 'none';
				} );

				const target = document.getElementById( button.dataset.dayTarget );
				if ( target ) {
					target.style.display = '';
				}
			} );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
