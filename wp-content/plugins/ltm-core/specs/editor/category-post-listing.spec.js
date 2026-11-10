/**
 * Tests for the latitudemedia/category-post-listing editor preview: links in
 * the ServerSideRender preview don't navigate away from the editor, except
 * the paginated-list pager, which re-renders the preview at the clicked page.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const BLOCK_NAME = 'latitudemedia/category-post-listing';

/**
 * Scopes to wherever block content actually renders — see the matching
 * helper in featured-single-item-block.spec.js for why both canvases are checked.
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

test.describe( 'Category Post Listing Block', () => {
	let categoryId;

	test.beforeEach( async ( { requestUtils } ) => {
		const category = await requestUtils.rest( {
			path: '/wp/v2/categories',
			method: 'POST',
			data: { name: `CPL Pager ${ Date.now() }` },
		} );
		categoryId = category.id;

		// The paginated-list layout shows 8 posts per page, newest first, so
		// 9 posts puts "CPL Post 1" (the oldest) alone on page 2.
		for ( let i = 1; i <= 9; i++ ) {
			await requestUtils.rest( {
				path: '/wp/v2/posts',
				method: 'POST',
				data: {
					title: `CPL Post ${ i }`,
					status: 'publish',
					categories: [ categoryId ],
					date: `2026-01-${ String( i ).padStart( 2, '0' ) }T12:00:00`,
				},
			} );
		}
	} );

	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
		await requestUtils.rest( {
			path: `/wp/v2/categories/${ categoryId }`,
			method: 'DELETE',
			params: { force: true },
		} );
	} );

	test( 'pager links page the preview without navigating away', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();
		await editor.insertBlock( {
			name: BLOCK_NAME,
			attributes: {
				categories: [ categoryId ],
				layout: 'paginated-list',
			},
		} );

		const editorContent = await getEditorContent( page, editor );
		const listing = editorContent.locator( '.ltm-category-post-listing' );
		await expect( listing.getByText( 'CPL Post 9' ) ).toBeVisible();
		await expect(
			listing.getByText( 'CPL Post 1', { exact: true } )
		).toHaveCount( 0 );

		const editorUrl = page.url();
		await listing.locator( '.pager a', { hasText: '2' } ).click();

		await expect(
			listing.getByText( 'CPL Post 1', { exact: true } )
		).toBeVisible();
		await expect( listing.getByText( 'CPL Post 9' ) ).toHaveCount( 0 );
		expect( page.url() ).toBe( editorUrl );

		await listing.locator( '.pager a', { hasText: '1' } ).click();
		await expect( listing.getByText( 'CPL Post 9' ) ).toBeVisible();
		expect( page.url() ).toBe( editorUrl );
	} );

	test( 'post links in the preview do not navigate away', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();
		await editor.insertBlock( {
			name: BLOCK_NAME,
			attributes: { categories: [ categoryId ] },
		} );

		const editorContent = await getEditorContent( page, editor );
		const link = editorContent
			.locator( '.ltm-category-post-listing a[href]' )
			.first();
		await expect( link ).toBeVisible();

		const editorUrl = page.url();
		await link.click();

		await expect( link ).toBeVisible();
		expect( page.url() ).toBe( editorUrl );
	} );
} );
