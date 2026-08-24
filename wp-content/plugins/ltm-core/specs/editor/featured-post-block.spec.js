/**
 * Tests for the latitudemedia/featured-post-block editor experience: the
 * "Post" ComboboxControl search-as-you-type against the real
 * ltm-core/v1/featured-post/search REST endpoint (not mocked), and the
 * "Is from sponsor?" / "Stop this post from showing up..." toggles.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const BLOCK_NAME = 'latitudemedia/featured-post-block';

/**
 * Scopes to wherever block content actually renders. Gutenberg only uses
 * the [name="editor-canvas"] iframe when no apiVersion 1/2 blocks are
 * registered — locally, ACF Composer blocks force the non-iframed
 * fallback, but CI drops ACF Pro (see .github/workflows/ltm-core-tests.yml),
 * so the iframe is back there. Check which one is actually present rather
 * than assuming either way.
 *
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

test.describe( 'Featured Post Block', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
	} );

	test( 'shows a placeholder until a post is selected', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();
		await editor.insertBlock( { name: BLOCK_NAME } );

		// Scoped to the editor content (canvas or page, see
		// getEditorContent) rather than a plain page-wide getByText() — the
		// a11y-speak live region echoes the same Placeholder instructions
		// text elsewhere on the page, and an unscoped match would hit both.
		const editorContent = await getEditorContent( page, editor );
		await expect(
			editorContent.getByText(
				'Select a post in the block settings to feature it here.'
			)
		).toBeVisible();
	} );

	test( 'searching the Post combobox returns live results from the REST endpoint', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		await requestUtils.createPost( {
			title: 'Grid Resilience Deep Dive',
			status: 'publish',
		} );

		await admin.createNewPost();
		await editor.insertBlock( { name: BLOCK_NAME } );
		await editor.openDocumentSettingsSidebar();

		const combobox = page.getByRole( 'combobox', { name: 'Post' } );
		await combobox.fill( 'Grid Resilience' );

		await expect(
			page.getByRole( 'option', { name: 'Grid Resilience Deep Dive' } )
		).toBeVisible();
	} );

	test( 'selecting a result sets postId and renders the server-side preview', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		await requestUtils.createPost( {
			title: 'Grid Resilience Deep Dive',
			status: 'publish',
		} );

		await admin.createNewPost();
		await editor.insertBlock( { name: BLOCK_NAME } );
		await editor.openDocumentSettingsSidebar();

		const combobox = page.getByRole( 'combobox', { name: 'Post' } );
		await combobox.fill( 'Grid Resilience' );
		await page
			.getByRole( 'option', { name: 'Grid Resilience Deep Dive' } )
			.click();

		const [ block ] = ( await editor.getBlocks() ).filter(
			( b ) => b.name === BLOCK_NAME
		);
		expect( block.attributes.postId ).toBeGreaterThan( 0 );

		const editorContent = await getEditorContent( page, editor );
		await expect(
			editorContent.locator( '.ltm-featured-post-block' )
		).toBeVisible();
	} );

	test( '"Is from sponsor?" with no sponsor assigned disables the results', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();
		await editor.insertBlock( { name: BLOCK_NAME } );
		await editor.openDocumentSettingsSidebar();

		await page
			.getByRole( 'checkbox', { name: 'Is from sponsor?' } )
			.click();

		await expect(
			page.getByText(
				'This page has no sponsor assigned, so no posts are available.'
			)
		).toBeVisible();

		const combobox = page.getByRole( 'combobox', { name: 'Post' } );
		await combobox.fill( 'a' );
		await expect( page.getByRole( 'option' ) ).toHaveCount( 0 );
	} );
} );
