/**
 * Editor tests for acf/event-description-block's type2 preview
 * (src/event-description-block/editor.js): typing into the theme's "Form
 * text" meta box input must update the preview's form title live, without a
 * save, and survive ACF re-rendering the preview.
 *
 * Skips when ACF Pro is absent (CI drops it): no acf/* block is registered
 * and the "General event options" meta box does not exist.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const BLOCK = 'acf/event-description-block';
const FORM_TEXT_INPUT = '.acf-field[data-key="field_6713ae8de1570"] input';

/**
 * Same iframe-or-not helper as specs/editor/featured-post-block.spec.js.
 */
async function getEditorContent( page, editor ) {
	const hasCanvasIframe = await page
		.locator( '[name="editor-canvas"]' )
		.count();
	return hasCanvasIframe
		? editor.canvas
		: page.getByLabel( 'Editor content' );
}

const type2Markup = [
	`<!-- wp:${ BLOCK } {"name":"${ BLOCK }","data":{"title":"About","_title":"field_674595b6064e9","display":"1","_display":"field_674481518f9b5"},"mode":"preview","className":"is-style-type2"} -->`,
	'<!-- wp:paragraph --><p>Description copy.</p><!-- /wp:paragraph -->',
	`<!-- /wp:${ BLOCK } -->`,
].join( '\n' );

test.describe( 'Event description block editor', () => {
	const createdIds = [];

	test.beforeEach( async ( { requestUtils } ) => {
		const registered = await requestUtils
			.rest( { path: `/wp/v2/block-types/${ BLOCK }` } )
			.then( () => true )
			.catch( () => false );
		test.skip( ! registered, `${ BLOCK } is not registered (ACF Pro inactive).` );
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

	test( 'type2 preview mirrors the Form text meta box input live', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: {
				title: 'Type 2 Form Text Event',
				status: 'publish',
				content: type2Markup,
			},
		} );
		createdIds.push( event.id );

		await admin.editPost( event.id );
		const content = await getEditorContent( page, editor );
		const formTitle = content.locator(
			'.wp-block-acf-event-description-block .right-sidebar-layout .form-title'
		);
		await expect( formTitle ).toHaveCount( 1 );

		const input = page.locator( FORM_TEXT_INPUT );
		await input.fill( 'Register for the summit' );
		await expect( formTitle ).toHaveText( 'Register for the summit' );

		await input.fill( 'Save your seat' );
		await expect( formTitle ).toHaveText( 'Save your seat' );

		// Changing one of the block's OWN fields makes ACF fetch a fresh
		// preview, rendered from the (unchanged) saved meta. The live value
		// must be re-applied on top of it.
		// Two matches: the block wrapper and the preview's own root <div>
		// (render.php's wrapper class lands on both). The block is the outer one.
		await content
			.locator( '.wp-block-acf-event-description-block' )
			.first()
			.click();
		await editor.openDocumentSettingsSidebar();
		const blockTitle = page.locator(
			'.acf-field[data-key="field_674595b6064e9"] input'
		);
		await blockTitle.fill( 'ABOUT THE SUMMIT' );
		await expect(
			content.locator( '.wp-block-acf-event-description-block .bordered-title' )
		).toHaveText( 'ABOUT THE SUMMIT' );
		await expect( formTitle ).toHaveText( 'Save your seat' );

		// Nothing was saved: the live text is in the DOM, not the database.
		const saved = await requestUtils.rest( {
			path: `/wp/v2/events/${ event.id }`,
			params: { context: 'edit' },
		} );
		expect( saved.content.raw ).toBe( type2Markup );
	} );
} );
