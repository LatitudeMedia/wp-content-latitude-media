/**
 * Frontend tests for acf/events-list-block's server-side render
 * (src/events-list-block/render.php) and its view.js "load more".
 *
 * Primary render coverage for this block -- see
 * specs/frontend/event-description-block.spec.js for why PHPUnit cannot
 * provide it. The two main cases reproduce the block's only live usage, the
 * Events page (/events/), which has an "Upcoming events" instance (a few
 * events, all visible, no button) and a "Past events" instance (many events,
 * three visible, the rest revealed three at a time by "load more").
 *
 * Event dates, type and flags are ACF post meta on the `events` CPT, which the
 * REST API does not expose, so they are written with WP-CLI through wp-env.
 */

/**
 * External dependencies
 */
const { execFileSync } = require( 'child_process' );
const path = require( 'path' );

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const PLUGIN_ROOT = path.join( __dirname, '..', '..' );
const IMAGE = path.join( __dirname, '..', 'assets', 'image-and-text-logo.png' );

// Field keys from the theme's "General event options" group
// (group_6744b701b9197). get_field() needs the `_name` reference to apply the
// field's return format, e.g. start_date's `m/d/Y g:i a`.
const EVENT_FIELD_KEYS = {
	start_date: 'field_6713ad31e1567',
	end_date: 'field_6713ad5ee1568',
	event_type: 'field_6713ade8e156a',
	past_event: 'field_6713ad28e1566',
	gated: 'field_6713b049e1582',
};

const DAY_MS = 24 * 60 * 60 * 1000;

/**
 * @param {number} days Offset from now.
 * @return {Date} The shifted date.
 */
const daysFromNow = ( days ) => new Date( Date.now() + days * DAY_MS );

/**
 * @param {Date} date
 * @return {string} `Y-m-d H:i:s`, the format ACF stores date_time_picker values in.
 */
const toMetaDate = ( date ) =>
	date.toISOString().slice( 0, 19 ).replace( 'T', ' ' );

/**
 * @param {Date} date
 * @return {string} `M j, Y`, as print_event_start_date() renders it. The tests
 * site runs on UTC.
 */
const toDisplayDate = ( date ) =>
	date.toLocaleDateString( 'en-US', {
		month: 'short',
		day: 'numeric',
		year: 'numeric',
		timeZone: 'UTC',
	} );

/**
 * Writes each event's ACF meta in one WP-CLI call.
 *
 * `past_event` and `gated` are always written, even as 0: get_events_list()
 * filters them with `!=`, which excludes events that lack the key entirely.
 *
 * @param {Array<{id: number, meta: Object}>} events
 */
const setEventMeta = ( events ) => {
	const payload = Buffer.from(
		JSON.stringify( { events, keys: EVENT_FIELD_KEYS } )
	).toString( 'base64' );

	execFileSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-cli',
			'wp',
			'eval',
			`$data = json_decode( base64_decode( '${ payload }' ), true );
			foreach ( $data['events'] as $event ) {
				foreach ( $event['meta'] as $name => $value ) {
					update_post_meta( $event['id'], $name, $value );
					update_post_meta( $event['id'], '_' . $name, $data['keys'][ $name ] );
				}
			}`,
		],
		{ cwd: PLUGIN_ROOT, stdio: 'pipe' }
	);
};

/**
 * @param {Object} data Field values: title, type, display.
 * @return {string} Serialized block with ACF's flattened `name` / `_name` data.
 */
const blockMarkup = ( { title, type, display = '1' } ) => {
	const attrs = {
		name: 'acf/events-list-block',
		data: {
			title,
			_title: 'field_673faa95e2af4',
			type,
			_type: 'field_673fad446ef02',
			display,
			_display: 'field_673f9b6dd2722',
		},
		mode: 'preview',
	};

	return `<!-- wp:acf/events-list-block ${ JSON.stringify( attrs ) } /-->`;
};

test.describe( 'Events list block frontend', () => {
	const createdPages = [];
	const createdEvents = [];
	let imageId;

	test.beforeAll( async ( { requestUtils } ) => {
		const media = await requestUtils.uploadMedia( IMAGE );
		imageId = media.id;
	} );

	test.afterAll( async ( { requestUtils } ) => {
		if ( imageId ) {
			await requestUtils.rest( {
				path: `/wp/v2/media/${ imageId }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	test.afterEach( async ( { requestUtils } ) => {
		while ( createdPages.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/pages/${ createdPages.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
		while ( createdEvents.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/events/${ createdEvents.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	/**
	 * Creates published events and writes their ACF meta.
	 *
	 * @param {Object} requestUtils
	 * @param {Array}  specs        { title, days, type?, pastEvent?, gated? }
	 * @return {Promise<Array>} The specs, each with its created `event` added.
	 */
	const createEvents = async ( requestUtils, specs ) => {
		for ( const spec of specs ) {
			spec.event = await requestUtils.rest( {
				path: '/wp/v2/events',
				method: 'POST',
				data: {
					title: spec.title,
					excerpt: `Excerpt for ${ spec.title }`,
					status: 'publish',
					featured_media: imageId,
				},
			} );
			createdEvents.push( spec.event.id );
		}

		setEventMeta(
			specs.map( ( spec ) => ( {
				id: spec.event.id,
				meta: {
					start_date: toMetaDate( daysFromNow( spec.days ) ),
					end_date: toMetaDate( daysFromNow( spec.days ) ),
					event_type: spec.type ?? 'webinar',
					past_event: spec.pastEvent ?? 0,
					gated: spec.gated ?? 0,
				},
			} ) )
		);

		return specs;
	};

	const createPage = async ( requestUtils, title, content ) => {
		const page = await requestUtils.rest( {
			path: '/wp/v2/pages',
			method: 'POST',
			data: { title, status: 'publish', content },
		} );
		createdPages.push( page.id );
		return page;
	};

	test( 'upcoming: renders every upcoming event in date order with no load more', async ( {
		page,
		requestUtils,
	} ) => {
		// Created out of order to prove the list sorts by start date.
		const [ third, first, second ] = await createEvents( requestUtils, [
			{ title: 'Upcoming Third', days: 30 },
			{ title: 'Upcoming First', days: 10, type: 'frontier-forum' },
			{ title: 'Upcoming Second', days: 20 },
			{ title: 'Already Happened', days: -10 },
			{ title: 'Gated Upcoming', days: 15, gated: 1 },
		] );

		const listPage = await createPage(
			requestUtils,
			'Upcoming Events List',
			blockMarkup( { title: 'Upcoming events', type: 'upcoming' } )
		);
		await page.goto( listPage.link );

		const block = page.locator( '.wp-block-acf-events-list-block' );
		await expect( block ).toHaveClass( /content-block/ );
		await expect( block ).toHaveClass( /three-events-section/ );
		await expect( block.locator( '.bordered-title.green' ) ).toHaveText(
			'Upcoming events'
		);

		const items = block.locator( '.three-events-section-wrapper ul > li' );
		await expect( items ).toHaveCount( 3 );
		await expect( block.locator( 'ul > li.hidden' ) ).toHaveCount( 0 );

		for ( const [ i, { title, days, type, event } ] of [
			first,
			second,
			third,
		].entries() ) {
			const item = items.nth( i );
			await expect( item ).toBeVisible();

			const image = item.locator( '.image-folder.green a' );
			await expect( image ).toHaveAttribute( 'href', event.link );
			await expect( image.locator( 'img' ) ).toHaveClass(
				/size-list-three-events/
			);

			const date = item.locator( '.content-folder .event-date.green' );
			await expect( date.locator( '.solid' ) ).toHaveText(
				type === 'frontier-forum' ? 'Frontier Forum' : 'webinar'
			);
			await expect( date.locator( '.date' ) ).toHaveText(
				toDisplayDate( daysFromNow( days ) )
			);

			const link = item.locator( '.content-folder a.title' );
			await expect( link ).toHaveText( title );
			await expect( link ).toHaveAttribute( 'href', event.link );
			await expect( item.locator( '.content-folder p' ) ).toHaveText(
				`Excerpt for ${ title }`
			);
		}

		await expect( block.locator( '.load-more-events' ) ).toHaveCount( 0 );
	} );

	test( 'past: shows three events and reveals the rest three at a time', async ( {
		page,
		requestUtils,
	} ) => {
		const past = await createEvents( requestUtils, [
			{ title: 'Past 1', days: -1 },
			{ title: 'Past 2', days: -2 },
			{ title: 'Past 3', days: -3 },
			{ title: 'Past 4', days: -4 },
			{ title: 'Past 5', days: -5 },
			{ title: 'Past 6', days: -6 },
			{ title: 'Past 7', days: -7 },
			// Future-dated but flagged as past: still listed, sorted by date.
			{ title: 'Flagged Past', days: 5, pastEvent: 1 },
			{ title: 'Still Upcoming', days: 10 },
			{ title: 'Gated Past', days: -3, gated: 1 },
		] );
		const expected = [ past[ 7 ], ...past.slice( 0, 7 ) ].map(
			( { title } ) => title
		);

		const listPage = await createPage(
			requestUtils,
			'Past Events List',
			blockMarkup( { title: 'Past events', type: 'past' } )
		);

		const restRequests = [];
		page.on( 'request', ( request ) => {
			if ( request.url().includes( '/wp-json/' ) ) {
				restRequests.push( request.url() );
			}
		} );

		await page.goto( listPage.link );

		const block = page.locator( '.wp-block-acf-events-list-block' );
		await expect( block.locator( '.bordered-title.green' ) ).toHaveText(
			'Past events'
		);

		const items = block.locator( '.three-events-section-wrapper ul > li' );
		await expect( items.locator( 'a.title' ) ).toHaveText( expected );

		const visible = block.locator( 'ul > li:not(.hidden)' );
		await expect( visible ).toHaveCount( 3 );
		await expect( block.locator( 'ul > li.hidden' ) ).toHaveCount( 5 );
		for ( let i = 0; i < expected.length; i++ ) {
			if ( i < 3 ) {
				await expect( items.nth( i ) ).toBeVisible();
			} else {
				await expect( items.nth( i ) ).toBeHidden();
			}
		}

		const button = block.locator( 'a.cta-button.green.load-more-events' );
		await expect( button ).toBeVisible();
		await expect( button ).toHaveText( 'load more' );

		await button.click();
		await expect( visible ).toHaveCount( 6 );
		await expect( button ).toBeVisible();

		await button.click();
		await expect( visible ).toHaveCount( 8 );
		await expect( items.last() ).toBeVisible();
		await expect( button ).toBeHidden();

		// The button is `href="#"`: view.js must swallow the navigation, and all
		// events were rendered up front, so nothing is fetched.
		await expect( page ).toHaveURL( listPage.link );
		expect( restRequests ).toEqual( [] );
	} );

	test( 'upcoming: shows the newsletter prompt when there are no events', async ( {
		page,
		requestUtils,
	} ) => {
		await createEvents( requestUtils, [
			{ title: 'Only In The Past', days: -10 },
		] );

		const listPage = await createPage(
			requestUtils,
			'Empty Upcoming Events List',
			blockMarkup( { title: 'Upcoming events', type: 'upcoming' } )
		);
		await page.goto( listPage.link );

		const block = page.locator( '.wp-block-acf-events-list-block' );
		await expect( block.locator( 'ul' ) ).toHaveCount( 0 );

		const empty = block.locator( '.upcoming-events-empty' );
		await expect( empty ).toContainText( 'More events coming soon.' );
		await expect( empty.locator( 'a' ) ).toHaveAttribute(
			'href',
			'/newsletter'
		);
	} );

	test( 'renders nothing on the frontend when display is off', async ( {
		page,
		requestUtils,
	} ) => {
		await createEvents( requestUtils, [
			{ title: 'Hidden Block Event', days: -10 },
		] );

		const listPage = await createPage(
			requestUtils,
			'Hidden Events List',
			blockMarkup( { title: 'Past events', type: 'past', display: '0' } )
		);
		await page.goto( listPage.link );

		await expect(
			page.locator( '.wp-block-acf-events-list-block' )
		).toHaveCount( 0 );
	} );
} );
