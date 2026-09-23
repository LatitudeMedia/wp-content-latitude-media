/**
 * Front-end behaviour for the Event Navigation Menu block, ported from the
 * theme's global custom.js (jQuery) when the block moved into ltm-core:
 *
 * - nav links and buttons pointing at an in-page `#anchor` smooth-scroll to
 *   it (offset for the sticky header) instead of jumping;
 * - the clicked nav link becomes the `.active` one;
 * - when the site header shows an alert banner, the block gets
 *   `has-alert-banner` so its sticky `top` accounts for the extra height.
 *
 * Click handling uses event delegation (one listener on `document`) — see
 * event-agenda-v2-block/view.js for why.
 */

( function () {
	const SCROLL_OFFSET = 300;

	function scrollToAnchor( link, event ) {
		const href = link.getAttribute( 'href' );

		if ( ! href || ! href.startsWith( '#' ) || href.length < 2 ) {
			return;
		}

		const target = document.getElementById( href.slice( 1 ) );

		if ( ! target ) {
			return;
		}

		event.preventDefault();
		window.scrollTo( {
			top: target.getBoundingClientRect().top + window.scrollY - SCROLL_OFFSET,
			behavior: 'smooth',
		} );
	}

	document.addEventListener( 'click', function ( event ) {
		const navLink = event.target.closest( '.navigation-menu-section .navigation-menu-links a' );

		if ( navLink ) {
			scrollToAnchor( navLink, event );
			navLink
				.closest( '.navigation-menu-links' )
				.querySelectorAll( 'a' )
				.forEach( function ( other ) {
					other.classList.remove( 'active' );
				} );
			navLink.classList.add( 'active' );
			return;
		}

		const button = event.target.closest( '.navigation-menu-section .buttons-container .nav-button' );

		if ( button ) {
			scrollToAnchor( button, event );
		}
	} );

	function flagAlertBanner() {
		if ( ! document.querySelector( 'header .header-wrapper .alert-banner' ) ) {
			return;
		}

		document.querySelectorAll( '.navigation-menu-section' ).forEach( function ( section ) {
			section.classList.add( 'has-alert-banner' );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', flagAlertBanner );
	} else {
		flagAlertBanner();
	}
} )();
