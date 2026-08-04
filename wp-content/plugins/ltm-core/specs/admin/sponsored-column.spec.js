/**
 * Tests for the "Sponsored" column on the Posts admin list table
 * (LTMCore\PostTypes\Sponsors::add_sponsored_column/render_sponsored_column).
 *
 * Only the real-sponsor and not-sponsored cases are covered here — the
 * legacy ACF toggle case relies on ACF field group definitions that only
 * live in the database (see TESTING.md's "Known gap: ACF field groups"),
 * so there's no reliable way to seed that fixture through the UI in a fresh
 * instance. That case is covered instead by
 * tests/PostTypes/SponsorsTest.php, which sets the field value directly.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Sponsored column', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
		await requestUtils.deleteAllPosts( 'sponsors' );
	} );

	test( 'is present with sponsor / not-sponsored values', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		await requestUtils.rest( {
			path: '/wp/v2/sponsors',
			method: 'POST',
			data: { title: 'Acme Corp', status: 'publish' },
		} );

		const sponsoredPost = await requestUtils.createPost( {
			title: 'Sponsored Post',
			status: 'publish',
		} );
		await requestUtils.createPost( {
			title: 'Unsponsored Post',
			status: 'publish',
		} );

		await admin.editPost( sponsoredPost.id );
		await page.locator( 'select#ltm_sponsor' ).selectOption( { label: 'Acme Corp' } );

		// "Is Sponsored By" is a classic meta box, saved via a background
		// request separate from the main REST save — wait for it rather than
		// racing the admin list table visit below against it.
		const metaBoxesSaved = page.waitForResponse(
			( response ) =>
				response.url().includes( '/wp-admin/post.php' ) &&
				response.request().method() === 'POST'
		);
		await page.getByRole( 'button', { name: 'Save', exact: false } ).first().click();
		await metaBoxesSaved;

		await admin.visitAdminPage( 'edit.php' );

		await expect(
			page.locator( 'th#sponsored, td.sponsored' ).first()
		).toBeVisible();

		// exact: true — "Sponsored Post" would otherwise case-insensitively
		// substring-match the "Unsponsored Post" link too.
		const sponsoredRow = page.locator( 'tr', {
			has: page.getByRole( 'link', { name: 'Sponsored Post', exact: true } ),
		} );
		await expect( sponsoredRow.locator( 'td.sponsored' ) ).toHaveText(
			'Acme Corp'
		);

		const unsponsoredRow = page.locator( 'tr', {
			has: page.getByRole( 'link', { name: 'Unsponsored Post', exact: true } ),
		} );
		await expect( unsponsoredRow.locator( 'td.sponsored' ) ).toHaveText(
			'Not sponsored'
		);
	} );
} );
