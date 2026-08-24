/**
 * Frontend tests for latitudemedia/featured-post-block's server-side render
 * (src/featured-post-block/render.php). Primary coverage for that file —
 * deliberately not unit-tested in PHPUnit, since it pulls in the theme's
 * get_wrap_rows_from_template()/Page_Data()/post-item template-part chain.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const BLOCK_NAME = 'latitudemedia/featured-post-block';

test.describe( 'Featured Post Block frontend', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
	} );

	test( 'renders the referenced published post as a card', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const featuredPost = await requestUtils.createPost( {
			title: 'Grid Resilience Deep Dive',
			status: 'publish',
		} );

		await admin.createNewPost( { title: 'Page With Featured Post' } );
		await editor.insertBlock( {
			name: BLOCK_NAME,
			attributes: { postId: featuredPost.id },
		} );

		const postId = await editor.publishPost();
		await page.goto( `/?p=${ postId }` );

		const card = page.locator( '.ltm-featured-post-block' );
		await expect( card ).toBeVisible();
		await expect(
			card.getByText( 'Grid Resilience Deep Dive' )
		).toBeVisible();
	} );

	test( 'renders nothing for a draft postId', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const draftPost = await requestUtils.createPost( {
			title: 'Not Published Yet',
			status: 'draft',
		} );

		await admin.createNewPost( { title: 'Page With Draft Feature' } );
		await editor.insertBlock( {
			name: BLOCK_NAME,
			attributes: { postId: draftPost.id },
		} );

		const postId = await editor.publishPost();
		await page.goto( `/?p=${ postId }` );

		await expect( page.locator( '.ltm-featured-post-block' ) ).toHaveCount(
			0
		);
	} );
} );
