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

		// Not editor.canvas: several ACF Composer blocks on this site are
		// registered with apiVersion 2, which forces Gutenberg to fall back to
		// a non-iframed editor — there is no [name="editor-canvas"] frame here,
		// so block content renders directly in the page. Scoped to
		// "Editor content" — the a11y-speak live region echoes the same
		// Placeholder instructions text, and getByText() alone matches both.
		await expect(
			page
				.getByLabel( 'Editor content' )
				.getByText(
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

		// Not editor.canvas — see the note in the first test above.
		await expect( page.locator( '.ltm-featured-post-block' ) ).toBeVisible();
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

	test( '"Stop this post from showing up in other blocks" toggles excludeFromOtherBlocks', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();
		await editor.insertBlock( { name: BLOCK_NAME } );
		await editor.openDocumentSettingsSidebar();

		// Attribute defaults to true.
		let [ block ] = ( await editor.getBlocks() ).filter(
			( b ) => b.name === BLOCK_NAME
		);
		expect( block.attributes.excludeFromOtherBlocks ).toBe( true );

		await page
			.getByRole( 'checkbox', {
				name: 'Stop this post from showing up in other blocks on this same page?',
			} )
			.click();

		[ block ] = ( await editor.getBlocks() ).filter(
			( b ) => b.name === BLOCK_NAME
		);
		expect( block.attributes.excludeFromOtherBlocks ).toBe( false );
	} );
} );
