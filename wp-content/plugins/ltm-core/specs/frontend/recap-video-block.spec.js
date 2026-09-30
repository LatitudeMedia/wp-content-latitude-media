/**
 * Frontend tests for acf/recap-video-block's server-side render
 * (src/recap-video-block/render.php) and stylesheet.
 *
 * Primary render coverage for this block -- see
 * specs/frontend/event-description-block.spec.js for why PHPUnit cannot
 * provide it.
 *
 * The fixture replicates the block on the live "Optimizing energy storage with
 * advanced analytics | Recording" event (post 2130): its block attributes are
 * copied verbatim, including ACF's flattened `fieldname`/`_fieldname` data, the
 * padding style and the `&` in the title. Only the video URL is swapped for
 * another test event's permalink: core answers oEmbed for its own site's URLs
 * without an HTTP request (wp_filter_pre_oembed_result), so the block still gets
 * a real provider <iframe> with no network.
 *
 * The expected CSS values were measured with getComputedStyle() on the live
 * pages while the block was still in the theme, at 1280px and at 375px. They pin
 * the migrated stylesheet to the look the block had before the move.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const TITLE =
	'Fluence experts showcase the cutting-edge digital tools redefining energy storage O&M.';

const liveData = ( video ) => ( {
	title: TITLE,
	_title: 'field_6759d1f983e50',
	video,
	_video: 'field_6759d1fe83e51',
	display: '1',
	_display: 'field_6759d0706f205',
} );

const DARK_BLUE = 'rgb(15, 30, 66)';

const DESKTOP_STYLES = {
	'': {
		'margin-bottom': '64px',
		'padding-top': '24px',
		'padding-bottom': '24px',
	},
	h2: {
		'font-size': '48px',
		'line-height': '67px',
		'text-align': 'center',
		'margin-bottom': '56px',
		color: DARK_BLUE,
	},
	'.recap-iframe-folder': { position: 'relative' },
	'.recap-iframe-folder iframe': {
		position: 'absolute',
		top: '0px',
		left: '0px',
		'border-top-style': 'none',
	},
};

// What changes below the theme's `below_mob` breakpoint.
const MOBILE_STYLES = {
	h2: { 'font-size': '32px', 'line-height': '45px' },
};

/**
 * Serializes the block the way core's serialize_block_attributes() does.
 *
 * @param {Object} attrs Block attributes, merged over the live ones.
 * @return {string} Block markup.
 */
const blockMarkup = ( attrs ) => {
	const json = JSON.stringify( {
		name: 'acf/recap-video-block',
		mode: 'edit',
		style: {
			spacing: {
				padding: {
					top: 'var:preset|spacing|50',
					bottom: 'var:preset|spacing|50',
				},
			},
		},
		...attrs,
	} )
		.replace( /--/g, '\\u002d\\u002d' )
		.replace( /</g, '\\u003c' )
		.replace( />/g, '\\u003e' )
		.replace( /&/g, '\\u0026' )
		.replace( /\\"/g, '\\u0022' );

	return `<!-- wp:acf/recap-video-block ${ json } /-->`;
};

/**
 * @param {import('@playwright/test').Locator} block
 * @param {Object}                             styles Selector (relative to the block) => CSS.
 */
const expectStyles = async ( block, styles ) => {
	for ( const [ selector, props ] of Object.entries( styles ) ) {
		const el = selector ? block.locator( selector ).first() : block;
		for ( const [ prop, value ] of Object.entries( props ) ) {
			await expect(
				el,
				`${ selector || 'wrapper' } ${ prop }`
			).toHaveCSS( prop, value );
		}
	}
};

/**
 * The folder's padding-top reserves a 16:9-ish box and the iframe fills the
 * folder. The ratio is checked on padding-top rather than on the rendered
 * height: core's self-embed also emits a <blockquote> fallback next to the
 * iframe that adds to the folder's height, which a YouTube embed on the live
 * site does not.
 *
 * @param {import('@playwright/test').Locator} block
 */
const expectVideoFillsFolder = async ( block ) => {
	const folder = block.locator( '.recap-iframe-folder' );
	const folderBox = await folder.boundingBox();
	const iframeBox = await block
		.locator( '.recap-iframe-folder iframe' )
		.boundingBox();
	const paddingTop = await folder.evaluate( ( el ) =>
		parseFloat( window.getComputedStyle( el ).paddingTop )
	);

	expect( paddingTop / folderBox.width ).toBeCloseTo( 0.562, 3 );
	expect( iframeBox ).toEqual( folderBox );
};

/**
 * Core's self-embed iframe carries an inline `position: absolute; visibility:
 * hidden;` (lifted by wp-embed.js once the embed loads) that would mask the
 * stylesheet's own iframe rules. A YouTube iframe has no inline style, so strip
 * it to test what the block's CSS alone does.
 *
 * @param {import('@playwright/test').Locator} block
 */
const stripEmbedInlineStyle = ( block ) =>
	block
		.locator( '.recap-iframe-folder iframe' )
		.evaluate( ( el ) => el.removeAttribute( 'style' ) );

test.describe( 'Recap video block frontend', () => {
	const createdIds = [];
	let videoUrl;

	const createEvent = async ( requestUtils, title, content ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: { title, status: 'publish', content },
		} );
		createdIds.push( event.id );
		return event;
	};

	test.beforeEach( async ( { requestUtils } ) => {
		videoUrl = ( await createEvent( requestUtils, 'Embedded Event', '' ) )
			.link;
	} );

	test.afterEach( async ( { requestUtils } ) => {
		while ( createdIds.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/events/${ createdIds.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	test( 'replicates the live recap video block on desktop', async ( {
		page,
		requestUtils,
	} ) => {
		await page.setViewportSize( { width: 1280, height: 900 } );
		const event = await createEvent(
			requestUtils,
			'Recap Video',
			blockMarkup( {
				data: liveData( videoUrl ),
				anchor: 'recap',
				className: 'extra-class',
			} )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-recap-video-block' );
		await stripEmbedInlineStyle( block );
		await expect( block ).toHaveAttribute( 'id', 'recap' );
		for ( const cls of [
			'content-block',
			'recap-video-section',
			'extra-class',
		] ) {
			await expect( block ).toHaveClass( new RegExp( `\\b${ cls }\\b` ) );
		}

		const wrapper = block.locator(
			'> .container-narrow > .recap-video-section-wrapper'
		);
		await expect( wrapper.locator( '> h2' ) ).toHaveText( TITLE );
		await expect(
			wrapper.locator( '> .recap-iframe-folder > iframe' )
		).toHaveAttribute( 'src', /\/embed\/?/ );

		await expectStyles( block, DESKTOP_STYLES );
		await expectVideoFillsFolder( block );
	} );

	test( 'shrinks the heading on mobile', async ( { page, requestUtils } ) => {
		await page.setViewportSize( { width: 375, height: 900 } );
		const event = await createEvent(
			requestUtils,
			'Recap Video Mobile',
			blockMarkup( { data: liveData( videoUrl ) } )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-recap-video-block' );
		await stripEmbedInlineStyle( block );
		await expectStyles( block, {
			...DESKTOP_STYLES,
			h2: { ...DESKTOP_STYLES.h2, ...MOBILE_STYLES.h2 },
		} );
		await expectVideoFillsFolder( block );
	} );

	test( 'renders nothing when display is off', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Hidden Recap Video',
			blockMarkup( { data: { ...liveData( videoUrl ), display: '0' } } )
		);

		await page.goto( event.link );

		await expect( page.locator( '.recap-video-section' ) ).toHaveCount( 0 );
	} );

	test( 'renders without an id or warnings when no anchor or padding is set', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Unanchored Recap Video',
			blockMarkup( { data: liveData( videoUrl ), style: undefined } )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-recap-video-block' );
		await expect( block ).toBeVisible();
		await expect( block ).not.toHaveAttribute( 'id', /.*/ );
		await expect( block ).toHaveCSS( 'padding-top', '0px' );
		await expect( page.locator( 'body' ) ).not.toContainText(
			/Warning:|Undefined array key/
		);
	} );
} );
