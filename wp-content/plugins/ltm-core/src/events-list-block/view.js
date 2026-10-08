/**
 * Front-end "load more" for the Events list block, ported from the theme's
 * src/assets/js/blocks/load-more-events.js (jQuery) when the block moved into
 * ltm-core.
 *
 * render.php outputs every event up front with items after the third marked
 * `hidden`; each click reveals the next three in the button's own list and
 * hides the button once none are left. No request is made.
 *
 * Click handling uses event delegation (one listener on `document`) — see
 * event-agenda-v2-block/view.js for why.
 */

( function () {
	const PAGE_SIZE = 3;

	document.addEventListener( 'click', ( event ) => {
		const button = event.target.closest( '.load-more-events' );

		if ( ! button ) {
			return;
		}

		event.preventDefault();

		const section = button.closest( '.three-events-section' );
		const hidden = section ? section.querySelectorAll( 'ul .hidden' ) : [];

		Array.from( hidden )
			.slice( 0, PAGE_SIZE )
			.forEach( ( item ) => item.classList.remove( 'hidden' ) );

		if ( hidden.length <= PAGE_SIZE ) {
			button.classList.add( 'hidden' );
		}
	} );
} )();
