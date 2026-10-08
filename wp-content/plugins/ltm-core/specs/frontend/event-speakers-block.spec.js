/**
 * Frontend tests for acf/event-speakers-block's server-side render
 * (src/event-speakers-block/render.php).
 *
 * Primary render coverage for this block -- see
 * specs/frontend/event-description-block.spec.js for why PHPUnit cannot
 * provide it.
 *
 * Two migration-specific risks are what this file exists to catch:
 *  - render.php swapped the theme's get_published_posts_by_ids() helper for a
 *    plain WP_Query, so the editor's chosen speaker order (post__in) must hold;
 *  - the block ships no JS -- the modal is driven by the theme's global
 *    custom.js binding .js-modal-open / .js-modal-close, so that cross-boundary
 *    wiring has to keep working against plugin-rendered markup.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

// Deliberately not alphabetical: the block must preserve this exact order.
const SPEAKERS = [ 'Zoe Winters', 'Adrian Cole', 'Mira Patel' ];

const blockMarkup = ( speakerIds, { display = '1', showReadMore = '1' } = {} ) => {
	const attrs = {
		name: 'acf/event-speakers-block',
		data: {
			title: 'Featured speakers',
			_title: 'field_674597fdb07ad',
			speakers: speakerIds,
			_speakers: 'field_67459804b07ae',
			display,
			_display: 'field_67449c4ecdcda',
			show_read_more_button: showReadMore,
			_show_read_more_button: 'field_6746402d35367',
		},
		mode: 'preview',
	};

	return `<!-- wp:acf/event-speakers-block ${ JSON.stringify( attrs ) } /-->`;
};

test.describe( 'Event speakers block frontend', () => {
	const createdEvents = [];
	const createdSpeakers = [];

	const createSpeakers = async ( requestUtils ) => {
		const ids = [];
		for ( const name of SPEAKERS ) {
			const speaker = await requestUtils.rest( {
				path: '/wp/v2/speakers',
				method: 'POST',
				data: {
					title: name,
					status: 'publish',
					content: `<p>${ name } bio copy.</p>`,
				},
			} );
			createdSpeakers.push( speaker.id );
			ids.push( speaker.id );
		}
		return ids;
	};

	const createEvent = async ( requestUtils, title, content ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: { title, status: 'publish', content },
		} );
		createdEvents.push( event.id );
		return event;
	};

	test.afterEach( async ( { requestUtils } ) => {
		for ( const [ path, ids ] of [
			[ 'events', createdEvents ],
			[ 'speakers', createdSpeakers ],
		] ) {
			while ( ids.length ) {
				await requestUtils.rest( {
					path: `/wp/v2/${ path }/${ ids.pop() }`,
					method: 'DELETE',
					params: { force: true },
				} );
			}
		}
	} );

	test( 'renders one card per speaker, in the editor-chosen order', async ( {
		page,
		requestUtils,
	} ) => {
		const speakerIds = await createSpeakers( requestUtils );
		const event = await createEvent(
			requestUtils,
			'Speakers Event',
			blockMarkup( speakerIds )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-speakers-block' );
		await expect( block ).toHaveClass( /content-block/ );
		await expect( block ).toHaveClass( /our-team-section/ );
		await expect( block.locator( '.bordered-title' ) ).toHaveText(
			'Featured speakers'
		);

		const cards = block.locator( 'ul.team > li' );
		await expect( cards ).toHaveCount( SPEAKERS.length );

		// post__in ordering, not post date / title ordering.
		await expect( block.locator( 'ul.team .content-folder .name' ) ).toHaveText(
			SPEAKERS
		);
	} );

	test( 'pairs each card with its own modal and opens it on click', async ( {
		page,
		requestUtils,
	} ) => {
		const speakerIds = await createSpeakers( requestUtils );
		const event = await createEvent(
			requestUtils,
			'Speakers Modal Event',
			blockMarkup( speakerIds )
		);

		await page.goto( event.link );

		const firstCard = page.locator( 'ul.team > li' ).first();
		const trigger = firstCard.locator( 'a.name.js-modal-open' );
		const href = await trigger.getAttribute( 'href' );

		// The anchor must point at the modal rendered alongside it. Matched by
		// attribute rather than `#id` because uniqid() can start with a digit,
		// which is not a valid CSS id selector.
		const modal = firstCard.locator(
			`.modal-content[id="${ href.slice( 1 ) }"]`
		);
		await expect( modal ).toHaveCount( 1 );
		await expect( modal.locator( 'h3' ) ).toHaveText( SPEAKERS[ 0 ] );
		await expect( modal ).toBeHidden();

		// Driven by the theme's global custom.js, which this block relies on.
		await trigger.click();
		await expect( modal ).toBeVisible();

		await modal.locator( '.strict-button.js-modal-close' ).click();
		await expect( modal ).toBeHidden();
	} );

	test( 'omits the read more link when the toggle is off', async ( {
		page,
		requestUtils,
	} ) => {
		const speakerIds = await createSpeakers( requestUtils );
		const event = await createEvent(
			requestUtils,
			'Speakers No Read More Event',
			blockMarkup( speakerIds, { showReadMore: '0' } )
		);

		await page.goto( event.link );

		await expect( page.locator( 'ul.team .more-link' ) ).toHaveCount( 0 );
		// The cards themselves still render.
		await expect( page.locator( 'ul.team > li' ) ).toHaveCount(
			SPEAKERS.length
		);
	} );

	test( 'renders nothing on the frontend when display is off', async ( {
		page,
		requestUtils,
	} ) => {
		const speakerIds = await createSpeakers( requestUtils );
		const event = await createEvent(
			requestUtils,
			'Hidden Speakers Event',
			blockMarkup( speakerIds, { display: '0' } )
		);

		await page.goto( event.link );

		await expect(
			page.locator( '.wp-block-acf-event-speakers-block' )
		).toHaveCount( 0 );
	} );
} );
