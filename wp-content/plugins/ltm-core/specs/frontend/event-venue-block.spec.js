/**
 * Frontend tests for acf/event-venue-block's server-side render
 * (src/event-venue-block/render.php) and stylesheet.
 *
 * Primary render coverage for this block -- see
 * specs/frontend/event-description-block.spec.js for why PHPUnit cannot
 * provide it.
 *
 * The main fixture replicates the block on the live Flex Summit 2026 event
 * (post 12513): its block attributes are copied verbatim, including ACF's
 * flattened `fieldname`/`_fieldname` data, and its event meta is reproduced.
 * Only the map embed's src is swapped for about:blank so the test needs no
 * network.
 *
 * The expected CSS values were measured with getComputedStyle() on the live
 * pages while the block was still in the theme (Flex Summit 2026 for blue,
 * Transition-AI 2024 for the default green, Transition-AI 2027 for pink), at
 * 1280px and at 375px. They pin the migrated stylesheet to the look the block
 * had before the move.
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

// Field keys from the theme's "General event options" group
// (group_6744b701b9197). get_field() needs the `_name` reference to apply the
// field's return format.
const EVENT_FIELD_KEYS = {
	start_date: 'field_6713ad31e1567',
	end_date: 'field_6713ad5ee1568',
	location: 'field_6713ae1fe156b',
	timezone: 'field_6713b050e1583',
};

const FLEX_SUMMIT_META = {
	start_date: '2026-10-14 08:00:00',
	end_date: '2026-10-15 17:00:00',
	location: 'Austin',
	timezone: 'CT',
};

const FLEX_SUMMIT_DATA = {
	additional_info:
		'Book your discounted hotel room for Flex Summit 2026 <a href="https://www.tripzero.events/flex-summit/2026" target="_blank">here</a>.',
	_additional_info: 'field_6745c801bdaff',
	embed_code:
		'<iframe src="about:blank" width="900" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
	_embed_code: 'field_6745c3f2c517f',
	location_details:
		'<a href="https://meetattexas.com/">AT&amp;T Hotel and Conference Center</a>\r\n\r\n&nbsp;',
	_location_details: 'field_6745c7fcbdafe',
	display: '1',
	_display: 'field_6744a68e8404b',
};

const BLUE = 'rgb(19, 81, 205)';
const GREEN = 'rgb(0, 180, 141)';
const PINK = 'rgb(198, 22, 141)';
const DARK_BLUE = 'rgb(15, 30, 66)';
const GREEN_SHADOW = 'rgb(204, 240, 232)';

const BODY_TEXT = {
	'font-size': '20px',
	'line-height': '24px',
	color: DARK_BLUE,
};

// Identical across the blue, green and pink live pages at 1280px, apart from
// the accent colours asserted separately.
const DESKTOP_STYLES = {
	'': { 'margin-bottom': '64px' },
	'.bordered-title': {
		'border-bottom-width': '5px',
		'border-bottom-style': 'solid',
		'margin-bottom': '32px',
		'font-size': '24px',
		'font-weight': '800',
		'text-transform': 'uppercase',
		color: DARK_BLUE,
	},
	'.additional-info p': { ...BODY_TEXT, 'font-weight': '400' },
	iframe: { 'margin-bottom': '32px', 'max-width': '100%' },
	'.venue-info': {
		display: 'flex',
		'flex-direction': 'row',
		'justify-content': 'space-between',
	},
	'.venue-info .date': {
		'padding-left': '24px',
		'border-left-width': '1px',
		'border-left-style': 'solid',
		'border-left-color': GREEN_SHADOW,
		'margin-bottom': '0px',
	},
	'.venue-info .location': {
		'padding-left': '24px',
		'border-left-width': '1px',
		'border-left-style': 'solid',
		'border-left-color': GREEN_SHADOW,
	},
	'.venue-info .date h2': { 'margin-bottom': '8px', 'font-size': '40px' },
	'.venue-info .location h2': { 'margin-bottom': '8px', 'font-size': '40px' },
	'.venue-date': { ...BODY_TEXT, 'font-weight': '400' },
	'address': { 'font-style': 'normal' },
	'address .place': { ...BODY_TEXT, 'margin-bottom': '8px' },
	'address p': { ...BODY_TEXT, 'margin-bottom': '0px' },
};

// What changes below the theme's `below_sm` breakpoint.
const MOBILE_STYLES = {
	'.venue-info': { 'flex-direction': 'column' },
	'.venue-info .date': { 'margin-bottom': '32px' },
	'.venue-info .date h2': { 'font-size': '24px' },
	'.venue-info .location h2': { 'font-size': '24px' },
};

/**
 * Writes ACF event meta (with each `_name` field reference) in one WP-CLI call.
 * Same approach as specs/frontend/events-list-block.spec.js.
 *
 * @param {number} id   Event post id.
 * @param {Object} meta Field name => value.
 */
const setEventMeta = ( id, meta ) => {
	const payload = Buffer.from(
		JSON.stringify( { id, meta, keys: EVENT_FIELD_KEYS } )
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
			foreach ( $data['meta'] as $name => $value ) {
				update_post_meta( $data['id'], $name, $value );
				update_post_meta( $data['id'], '_' . $name, $data['keys'][ $name ] );
			}`,
		],
		{ cwd: PLUGIN_ROOT, stdio: 'pipe' }
	);
};

/**
 * Serializes the block the way core's serialize_block_attributes() does, so the
 * HTML inside the ACF data cannot close the block comment early.
 *
 * @param {Object} attrs Block attributes, merged over the Flex Summit ones.
 * @return {string} Block markup.
 */
const blockMarkup = ( attrs = {} ) => {
	const json = JSON.stringify( {
		name: 'acf/event-venue-block',
		data: FLEX_SUMMIT_DATA,
		mode: 'edit',
		anchor: 'venue',
		className: 'blue-theme',
		...attrs,
	} )
		.replace( /--/g, '\\u002d\\u002d' )
		.replace( /</g, '\\u003c' )
		.replace( />/g, '\\u003e' )
		.replace( /&/g, '\\u0026' )
		.replace( /\\"/g, '\\u0022' );

	return `<!-- wp:acf/event-venue-block ${ json } /-->`;
};

/**
 * @param {import('@playwright/test').Locator} block
 * @param {Object}                             styles Selector (relative to the block) => CSS.
 */
const expectStyles = async ( block, styles ) => {
	for ( const [ selector, props ] of Object.entries( styles ) ) {
		const el = selector ? block.locator( selector ).first() : block;
		for ( const [ prop, value ] of Object.entries( props ) ) {
			await expect( el, `${ selector || 'wrapper' } ${ prop }` ).toHaveCSS(
				prop,
				value
			);
		}
	}
};

test.describe( 'Event venue block frontend', () => {
	const createdIds = [];

	const createEvent = async ( requestUtils, title, content ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: { title, status: 'publish', content },
		} );
		createdIds.push( event.id );
		setEventMeta( event.id, FLEX_SUMMIT_META );
		return event;
	};

	test.afterEach( async ( { requestUtils } ) => {
		while ( createdIds.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/events/${ createdIds.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	test( 'replicates the Flex Summit 2026 venue block on desktop', async ( {
		page,
		requestUtils,
	} ) => {
		await page.setViewportSize( { width: 1280, height: 900 } );
		const event = await createEvent(
			requestUtils,
			'Flex Summit Venue',
			blockMarkup()
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-venue-block' );
		await expect( block ).toHaveAttribute( 'id', 'venue' );
		for ( const cls of [
			'content-block',
			'venue-section',
			'green',
			'blue-theme',
		] ) {
			await expect( block ).toHaveClass( new RegExp( `\\b${ cls }\\b` ) );
		}

		// Same element order as the live page.
		const wrapper = block.locator(
			'> .container-narrow > .venue-section-wrapper'
		);
		await expect( wrapper.locator( '> *' ) ).toHaveCount( 4 );
		await expect( wrapper.locator( '> :nth-child(1)' ) ).toHaveClass(
			'bordered-title'
		);
		await expect( wrapper.locator( '> :nth-child(1)' ) ).toHaveText(
			'Venue'
		);
		await expect( wrapper.locator( '> :nth-child(2)' ) ).toHaveClass(
			'additional-info'
		);
		await expect( wrapper.locator( '> iframe:nth-child(3)' ) ).toHaveAttribute(
			'height',
			'450'
		);
		await expect( wrapper.locator( '> :nth-child(4)' ) ).toHaveClass(
			'venue-info'
		);

		await expect( block.locator( '.additional-info p' ) ).toHaveText(
			'Book your discounted hotel room for Flex Summit 2026 here.'
		);
		await expect(
			block.locator( '.additional-info a' )
		).toHaveAttribute( 'href', 'https://www.tripzero.events/flex-summit/2026' );

		const date = block.locator( '.venue-info > .date' );
		await expect( date.locator( 'h2' ) ).toHaveText( 'Date' );
		await expect( date.locator( '.venue-date' ) ).toHaveText(
			'October 14-15, 2026'
		);

		const location = block.locator( '.venue-info > .location' );
		await expect( location.locator( 'h2' ) ).toHaveText( 'Location' );
		await expect( location.locator( 'address > .place' ) ).toHaveText(
			'Austin'
		);
		await expect(
			location.getByRole( 'link', { name: 'AT&T Hotel and Conference Center' } )
		).toHaveAttribute( 'href', 'https://meetattexas.com/' );

		await expectStyles( block, DESKTOP_STYLES );
		await expectStyles( block, {
			'.bordered-title': { 'border-bottom-color': BLUE },
			'.additional-info a': { color: BLUE },
			'address a': { color: BLUE },
		} );
	} );

	test( 'stacks the date and location columns on mobile', async ( {
		page,
		requestUtils,
	} ) => {
		await page.setViewportSize( { width: 375, height: 900 } );
		const event = await createEvent(
			requestUtils,
			'Flex Summit Venue Mobile',
			blockMarkup()
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-venue-block' );
		await expectStyles( block, {
			...DESKTOP_STYLES,
			...Object.fromEntries(
				Object.entries( MOBILE_STYLES ).map( ( [ selector, props ] ) => [
					selector,
					{ ...DESKTOP_STYLES[ selector ], ...props },
				] )
			),
		} );
	} );

	// Each variant: [ className, title border colour, link colour ]. The
	// Date/Location column borders stay green for every one -- the wrapper's
	// hardcoded `green` class sets them, as it did in the theme.
	for ( const [ className, border, link ] of [
		[ 'is-style-blue-theme', BLUE, BLUE ],
		[ 'pink-theme', PINK, PINK ],
		[ 'is-style-pink-theme', PINK, PINK ],
		[ undefined, GREEN, GREEN ],
		[ 'is-style-default', GREEN, GREEN ],
	] ) {
		test( `colours the ${ className || 'default green' } variant`, async ( {
			page,
			requestUtils,
		} ) => {
			await page.setViewportSize( { width: 1280, height: 900 } );
			const event = await createEvent(
				requestUtils,
				`Venue ${ className || 'default' }`,
				blockMarkup( { className } )
			);

			await page.goto( event.link );

			const block = page.locator( '.wp-block-acf-event-venue-block' );
			await expectStyles( block, {
				'.bordered-title': { 'border-bottom-color': border },
				'.venue-info .date': { 'border-left-color': GREEN_SHADOW },
				'.venue-info .location': { 'border-left-color': GREEN_SHADOW },
				'.additional-info a': { color: link },
				'address a': { color: link },
			} );
		} );
	}

	test( 'renders nothing when display is off', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Hidden Venue',
			blockMarkup( { data: { ...FLEX_SUMMIT_DATA, display: '0' } } )
		);

		await page.goto( event.link );

		await expect( page.locator( '.venue-section' ) ).toHaveCount( 0 );
	} );

	test( 'renders without an id or warnings when no anchor is set', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Unanchored Venue',
			blockMarkup( { anchor: undefined } )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-venue-block' );
		await expect( block ).toBeVisible();
		await expect( block ).not.toHaveAttribute( 'id', /.*/ );
		await expect( page.locator( 'body' ) ).not.toContainText(
			/Warning:|Undefined array key/
		);
	} );
} );
