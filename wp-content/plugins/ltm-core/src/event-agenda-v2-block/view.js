/**
 * Front-end and editor-canvas behaviour for the Event Agenda V2 block:
 * clicking a day button switches which day's agenda is shown.
 *
 * Uses event delegation (one listener on `document`) rather than binding
 * directly to each button at script-load time, because in the block editor
 * the preview markup is injected asynchronously after this script has
 * already run (and may be re-rendered more than once) — direct binding would
 * only ever catch buttons that already existed at load time.
 */

( function () {
	document.addEventListener( 'click', function ( event ) {
		const button = event.target.closest( '.event-agenda-v2-day-button' );

		if ( ! button ) {
			return;
		}

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
} )();
