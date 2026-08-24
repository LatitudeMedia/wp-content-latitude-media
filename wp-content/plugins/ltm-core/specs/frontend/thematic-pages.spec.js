/**
 * Frontend tests for the thematic-pages CPT: its public /themes/{slug} URL
 * resolves, and it stays out of front-end search results
 * (exclude_from_search, LTMCore\PostTypes\ThematicPages::create_post_type()).
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Thematic Pages frontend', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts( 'thematic-pages' );
	} );

	test( '/themes/{slug} resolves for a published thematic page', async ( {
		page,
		requestUtils,
	} ) => {
		// A REST-created post has no blocks by default — the CPT's `template`
		// (latitudemedia/title-block) is only auto-inserted by the editor UI
		// on a new post, not by the REST API. The title only renders via that
		// block's inner core/post-title (see title-block.spec.js's "no title
		// field in the canvas" note), so supply it explicitly.
		const thematicPage = await requestUtils.rest( {
			path: '/wp/v2/thematic-pages',
			method: 'POST',
			data: {
				title: 'Grid Resilience',
				status: 'publish',
				content:
					'<!-- wp:latitudemedia/title-block --><!-- wp:post-title {"level":1} /--><!-- /wp:latitudemedia/title-block -->',
			},
		} );

		const response = await page.goto( `/themes/${ thematicPage.slug }/` );

		expect( response.status() ).toBeLessThan( 400 );
		await expect(
			page.getByText( 'Grid Resilience' ).first()
		).toBeVisible();
	} );

	test( 'is excluded from front-end search results', async ( {
		page,
		requestUtils,
	} ) => {
		const uniqueTitle = `Searchable Thematic Page ${ Date.now() }`;
		await requestUtils.rest( {
			path: '/wp/v2/thematic-pages',
			method: 'POST',
			data: { title: uniqueTitle, status: 'publish' },
		} );

		await page.goto( `/?s=${ encodeURIComponent( uniqueTitle ) }` );

		await expect( page.getByText( uniqueTitle ) ).toHaveCount( 0 );
	} );
} );
