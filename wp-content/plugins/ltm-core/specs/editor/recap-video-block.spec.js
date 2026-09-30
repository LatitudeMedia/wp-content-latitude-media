/**
 * Editor tests for acf/recap-video-block's canvas video preview
 * (src/recap-video-block/index.js): the oEmbed iframe must be moved into a
 * sandbox written from the top window, so its request carries a Referer --
 * YouTube refuses embeds without one (Error 153), and the blob: canvas sends
 * none. Also covers ACF re-rendering the preview.
 *
 * The video is another test event's permalink, which core embeds itself with
 * no network (see specs/frontend/recap-video-block.spec.js). The Referer rule
 * is the browser's, so a same-site embed proves it as well as YouTube would.
 *
 * wp-env runs acf-blocks-v2-iframe-compatibility, which forces the canvas out
 * of its iframe; production does not, so it is deactivated for this file to get
 * the real, iframed canvas. Safe because the suite runs on one worker.
 *
 * Skips when ACF Pro is absent (CI drops it): no acf/* block is registered.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const BLOCK = 'acf/recap-video-block';
const IFRAMELESS_PLUGIN = 'acf-blocks-v2-iframe-compatibility';

const blockMarkup = ( video, title ) =>
	`<!-- wp:${ BLOCK } ${ JSON.stringify( {
		name: BLOCK,
		data: {
			title,
			_title: 'field_6759d1f983e50',
			video,
			_video: 'field_6759d1fe83e51',
			display: '1',
			_display: 'field_6759d0706f205',
		},
		mode: 'preview',
	} ) } /-->`;

test.describe( 'Recap video block editor', () => {
	const createdIds = [];

	const createEvent = async ( requestUtils, title, content = '' ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: { title, status: 'publish', content },
		} );
		createdIds.push( event.id );
		return event;
	};

	test.beforeAll( async ( { requestUtils } ) => {
		await requestUtils.deactivatePlugin( IFRAMELESS_PLUGIN );
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await requestUtils.activatePlugin( IFRAMELESS_PLUGIN );
	} );

	test.beforeEach( async ( { requestUtils } ) => {
		const registered = await requestUtils
			.rest( { path: `/wp/v2/block-types/${ BLOCK }` } )
			.then( () => true )
			.catch( () => false );
		test.skip(
			! registered,
			`${ BLOCK } is not registered (ACF Pro inactive).`
		);
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

	test( 'loads the canvas video with a Referer, also after a re-render', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const first = await createEvent( requestUtils, 'First Embedded Event' );
		const second = await createEvent(
			requestUtils,
			'Second Embedded Event'
		);
		const event = await createEvent(
			requestUtils,
			'Recap Video Editor',
			blockMarkup( first.link, 'Recap' )
		);

		// Embed URL => Referer of each request for it.
		const referers = {};
		page.on( 'request', ( request ) => {
			for ( const { link } of [ first, second ] ) {
				if ( request.url().startsWith( `${ link }embed/` ) ) {
					( referers[ link ] ||= [] ).push(
						request.headers().referer
					);
				}
			}
		} );

		await admin.editPost( event.id );
		test.skip(
			! ( await page.locator( '[name="editor-canvas"]' ).count() ),
			'Editor canvas is not iframed, so there is no blob: document to work around.'
		);

		const sandboxedSrc = () =>
			editor.canvas
				.locator( '.recap-iframe-folder > iframe.recap-video-sandbox' )
				.evaluate(
					( el ) =>
						el.contentDocument.querySelector( 'iframe' )?.src ?? ''
				);

		await expect.poll( sandboxedSrc ).toContain( `${ first.link }embed/` );
		await expect
			.poll( () => referers[ first.link ] ?? [] )
			.toContainEqual( expect.stringMatching( /^http/ ) );

		// Changing the field makes ACF fetch and swap in a fresh preview.
		await page.evaluate(
			( [ name, video ] ) => {
				const { select, dispatch } = window.wp.data;
				const block = select( 'core/block-editor' )
					.getBlocks()
					.find( ( b ) => b.name === name );
				dispatch( 'core/block-editor' ).updateBlockAttributes(
					block.clientId,
					{ data: { ...block.attributes.data, video } }
				);
			},
			[ BLOCK, second.link ]
		);

		await expect.poll( sandboxedSrc ).toContain( `${ second.link }embed/` );
		await expect
			.poll( () => referers[ second.link ] ?? [] )
			.toContainEqual( expect.stringMatching( /^http/ ) );
		await expect(
			editor.canvas.locator(
				'.recap-iframe-folder > iframe:not(.recap-video-sandbox)'
			)
		).toHaveCount( 0 );
	} );
} );
