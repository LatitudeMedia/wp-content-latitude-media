/**
 * Tests for the "Is Sponsored By" meta box (LTMCore\Taxonomies\PostSponsor)
 * on both `post` and `thematic-pages` — selection round-trips through
 * save/reload, and saving replaces rather than appends the prior sponsor.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

/**
 * "Is Sponsored By" is a classic meta box; the block editor submits classic
 * meta box data via a separate background request to post.php after the
 * main REST save completes. Reloading before that request finishes reads
 * back the pre-save value, so wait for it explicitly rather than racing it.
 * @param page
 * @param label
 */
async function selectSponsor( page, label ) {
	await page.locator( 'select#ltm_sponsor' ).selectOption( { label } );

	const metaBoxesSaved = page.waitForResponse(
		( response ) =>
			response.url().includes( '/wp-admin/post.php' ) &&
			response.request().method() === 'POST'
	);
	await page
		.getByRole( 'button', { name: 'Save', exact: false } )
		.first()
		.click();
	await metaBoxesSaved;
}

test.describe( 'Sponsor meta box', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
		await requestUtils.deleteAllPosts( 'sponsors' );
		await requestUtils.deleteAllPosts( 'thematic-pages' );
	} );

	test( 'selection round-trips through save/reload on a post', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		await requestUtils.rest( {
			path: '/wp/v2/sponsors',
			method: 'POST',
			data: { title: 'Acme Corp', status: 'publish' },
		} );

		const post = await requestUtils.createPost( {
			title: 'A Post',
			status: 'draft',
		} );

		await admin.editPost( post.id );
		await selectSponsor( page, 'Acme Corp' );
		await page.reload();

		const selectedLabel = await page
			.locator( 'select#ltm_sponsor option:checked' )
			.textContent();
		expect( selectedLabel ).toBe( 'Acme Corp' );
	} );

	test( 'switching sponsors replaces rather than appends the prior one', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		await requestUtils.rest( {
			path: '/wp/v2/sponsors',
			method: 'POST',
			data: { title: 'First Sponsor', status: 'publish' },
		} );
		await requestUtils.rest( {
			path: '/wp/v2/sponsors',
			method: 'POST',
			data: { title: 'Second Sponsor', status: 'publish' },
		} );

		const post = await requestUtils.createPost( {
			title: 'A Post',
			status: 'draft',
		} );

		await admin.editPost( post.id );
		await selectSponsor( page, 'First Sponsor' );
		await page.reload();
		await selectSponsor( page, 'Second Sponsor' );
		await page.reload();

		const selectedLabel = await page
			.locator( 'select#ltm_sponsor option:checked' )
			.textContent();
		expect( selectedLabel ).toBe( 'Second Sponsor' );
	} );

	test( '"Not Sponsored" clears the assignment', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		await requestUtils.rest( {
			path: '/wp/v2/sponsors',
			method: 'POST',
			data: { title: 'Acme Corp', status: 'publish' },
		} );

		const post = await requestUtils.createPost( {
			title: 'A Post',
			status: 'draft',
		} );

		await admin.editPost( post.id );
		await selectSponsor( page, 'Acme Corp' );
		await page.reload();
		await selectSponsor( page, 'Not Sponsored' );
		await page.reload();

		const selectedLabel = await page
			.locator( 'select#ltm_sponsor option:checked' )
			.textContent();
		expect( selectedLabel ).toBe( 'Not Sponsored' );
	} );

	test( 'is also wired up on thematic-pages', async ( {
		page,
		requestUtils,
	} ) => {
		await requestUtils.rest( {
			path: '/wp/v2/sponsors',
			method: 'POST',
			data: { title: 'Acme Corp', status: 'publish' },
		} );

		const thematicPage = await requestUtils.rest( {
			path: '/wp/v2/thematic-pages',
			method: 'POST',
			data: { title: 'A Thematic Page', status: 'draft' },
		} );

		await page.goto(
			`/wp-admin/post.php?post=${ thematicPage.id }&action=edit`
		);
		await selectSponsor( page, 'Acme Corp' );
		await page.reload();

		const selectedLabel = await page
			.locator( 'select#ltm_sponsor option:checked' )
			.textContent();
		expect( selectedLabel ).toBe( 'Acme Corp' );
	} );
} );
