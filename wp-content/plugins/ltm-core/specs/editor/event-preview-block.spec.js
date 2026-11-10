/**
 * Editor tests for acf/event-preview-block's title, which render.php emits as
 * a locked core/post-title inner block: editing the Event's post title must
 * update the preview's <h1> live, without a save, and survive ACF
 * re-rendering the preview. Blocks saved before the inner block existed must
 * still show the title on the frontend.
 *
 * The Button row needs the Event's `link` meta: without it the editor
 * warns under the Rows checkboxes, with it the preview shows the button.
 *
 * Skips when ACF Pro is absent (CI drops it): no acf/* block is registered.
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

const BLOCK = 'acf/event-preview-block';

/**
 * Same iframe-or-not helper as specs/editor/featured-single-item-block.spec.js.
 * @param {import('@playwright/test').Page} page
 * @param {Object}                          editor
 */
async function getEditorContent( page, editor ) {
	const hasCanvasIframe = await page
		.locator( '[name="editor-canvas"]' )
		.count();
	return hasCanvasIframe
		? editor.canvas
		: page.getByLabel( 'Editor content' );
}

// Saved before the title became an inner block: self-closing, no children.
const legacyMarkup = `<!-- wp:${ BLOCK } {"name":"${ BLOCK }","data":{"display":"1","_display":"field_67447f65a31a7","rows":["title"],"_rows":"field_67473ac9a1f7c","subtitle":"Save the date","_subtitle":"field_67473b75a1f7d"},"mode":"preview"} /-->`;

test.describe( 'Event preview block editor', () => {
	const createdIds = [];

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

	test( 'preview title mirrors the edited post title live', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: {
				title: 'Original Event Title',
				status: 'publish',
				content: legacyMarkup,
			},
		} );
		createdIds.push( event.id );

		// Legacy content has no inner blocks: render.php falls back to a
		// plain heading.
		await page.goto( event.link );
		await expect(
			page.locator( '.single-event-hero-section h1' )
		).toHaveText( 'Original Event Title' );

		await admin.editPost( event.id );
		const content = await getEditorContent( page, editor );
		const heading = content.locator(
			'.wp-block-acf-event-preview-block h1'
		);
		await expect( heading ).toHaveText( 'Original Event Title' );

		await content
			.getByRole( 'textbox', { name: 'Add title' } )
			.fill( 'Renamed Event Title' );
		await expect( heading ).toHaveText( 'Renamed Event Title' );

		// Changing one of the block's OWN fields makes ACF fetch a fresh
		// preview. The inner block, and so the live title, must survive it.
		// Click the subtitle, not the block's centre: that is the inner
		// Title block, which would select it instead of the ACF block.
		await content
			.locator( '.wp-block-acf-event-preview-block .subtitle' )
			.click();
		await editor.openDocumentSettingsSidebar();
		await page
			.locator( '.acf-field[data-key="field_67473b75a1f7d"] input' )
			.fill( 'New subtitle' );
		await expect(
			content.locator( '.wp-block-acf-event-preview-block .subtitle' )
		).toHaveText( 'New subtitle' );
		await expect( heading ).toHaveText( 'Renamed Event Title' );

		// Nothing was saved yet: the live title is editor state only.
		const unsaved = await requestUtils.rest( {
			path: `/wp/v2/events/${ event.id }`,
			params: { context: 'edit' },
		} );
		expect( unsaved.title.raw ).toBe( 'Original Event Title' );

		// Once saved, the frontend title comes from the inner block.
		const postSaved = page.waitForResponse(
			( response ) =>
				response.url().includes( `/wp/v2/events/${ event.id }` ) &&
				response.request().method() === 'POST'
		);
		await page
			.getByRole( 'button', { name: 'Save', exact: false } )
			.first()
			.click();
		await postSaved;
		const saved = await requestUtils.rest( {
			path: `/wp/v2/events/${ event.id }`,
			params: { context: 'edit' },
		} );
		expect( saved.content.raw ).toContain( '<!-- wp:post-title' );

		await page.goto( saved.link );
		await expect(
			page.locator( '.single-event-hero-section h1' )
		).toHaveText( 'Renamed Event Title' );
	} );

	test( 'warns when Button is ticked but the Event has no link', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: {
				title: 'Linkless Event',
				status: 'publish',
				content: legacyMarkup,
			},
		} );
		createdIds.push( event.id );

		await admin.editPost( event.id );
		const content = await getEditorContent( page, editor );
		await content
			.locator( '.wp-block-acf-event-preview-block .subtitle' )
			.click();
		await editor.openDocumentSettingsSidebar();

		const rows = page.locator(
			'.acf-field[data-key="field_67473ac9a1f7c"]'
		);
		const notice = rows.locator( '.ltm-event-preview-link-notice' );
		const buttonRow = rows.getByRole( 'checkbox', { name: 'Button' } );

		await expect( buttonRow ).not.toBeChecked();
		await expect( notice ).toBeHidden();

		await buttonRow.check();
		await expect( notice ).toBeVisible();
		await expect( notice ).toContainText(
			'Link is missing from Event data.'
		);

		// The notice's link jumps to the Post tab's link field.
		await notice.getByRole( 'link' ).click();
		const linkInput = page.locator( '#acf-field_6713ae62e156d' );
		await expect( linkInput ).toBeFocused();

		await linkInput.fill( 'https://example.com/register' );
		await expect( notice ).toBeHidden();
	} );

	test( 'preview shows the button when ticked and the Event has a link', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: {
				title: 'Linked Event',
				status: 'publish',
				content: legacyMarkup,
			},
		} );
		createdIds.push( event.id );
		// "link" lives in the theme's "General event options" meta box and
		// isn't exposed over REST; write it as ACF stores it.
		execFileSync(
			'npx',
			[
				'wp-env',
				'run',
				'tests-cli',
				'wp',
				'eval',
				`update_post_meta( ${ event.id }, 'link', 'https://example.com/register' );
				update_post_meta( ${ event.id }, '_link', 'field_6713ae62e156d' );`,
			],
			{ cwd: path.resolve( __dirname, '../..' ), stdio: 'pipe' }
		);

		await admin.editPost( event.id );
		const content = await getEditorContent( page, editor );
		const button = content.locator(
			'.wp-block-acf-event-preview-block a.strict-button'
		);
		await expect( button ).toHaveCount( 0 );

		await content
			.locator( '.wp-block-acf-event-preview-block .subtitle' )
			.click();
		await editor.openDocumentSettingsSidebar();
		const rows = page.locator(
			'.acf-field[data-key="field_67473ac9a1f7c"]'
		);
		await rows.getByRole( 'checkbox', { name: 'Button' } ).check();

		await expect( button ).toHaveAttribute(
			'href',
			'https://example.com/register'
		);
		await expect(
			rows.locator( '.ltm-event-preview-link-notice' )
		).toBeHidden();
	} );
} );
