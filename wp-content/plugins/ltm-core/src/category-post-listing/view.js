/**
 * Front-end behaviour for the Category Post Listing block: intercepts
 * pager link clicks and swaps in the new page of posts via fetch()
 * instead of doing a full page navigation.
 */

( function () {
	function getContainers() {
		return document.querySelectorAll( '.ltm-category-post-listing' );
	}

	function loadPage( container, url, updateHistory ) {
		fetch( url )
			.then( function ( response ) {
				return response.text();
			} )
			.then( function ( html ) {
				const doc = new window.DOMParser().parseFromString( html, 'text/html' );
				const index = Array.prototype.indexOf.call( getContainers(), container );
				const newContainer = doc.querySelectorAll( '.ltm-category-post-listing' )[ index ];

				if ( ! newContainer ) {
					window.location.href = url;
					return;
				}

				container.innerHTML = newContainer.innerHTML;
				bindPager( container );

				if ( updateHistory ) {
					window.history.pushState( { ltmCategoryPostListing: true }, '', url );
				}
			} )
			.catch( function () {
				window.location.href = url;
			} );
	}

	function bindPager( container ) {
		const pager = container.querySelector( '.pager' );

		if ( ! pager ) {
			return;
		}

		pager.addEventListener( 'click', function ( event ) {
			const link = event.target.closest( 'a' );

			if ( ! link ) {
				return;
			}

			event.preventDefault();
			loadPage( container, link.href, true );
		} );
	}

	function init() {
		getContainers().forEach( bindPager );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

	window.addEventListener( 'popstate', function () {
		getContainers().forEach( function ( container ) {
			loadPage( container, window.location.href, false );
		} );
	} );
} )();
