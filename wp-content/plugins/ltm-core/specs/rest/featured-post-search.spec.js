/**
 * Tests for the ltm-core/v1/featured-post/search REST endpoint.
 *
 * Bypasses /wp/v2/posts deliberately, per the endpoint's own docblock, since
 * it needs to filter by the `sponsor` taxonomy which is not show_in_rest.
 * See tests/RestApi/FeaturedPostSearchTest.php for the sponsored-scoping
 * case (covered there directly against term meta, without needing a UI
 * round-trip to assign a sponsor).
 */

/**
 * External dependencies
 */
const { request } = require( '@playwright/test' );

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const ROUTE = '/ltm-core/v1/featured-post/search';

test.describe( 'Featured Post search REST endpoint', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
		await requestUtils.deleteAllPosts( 'thematic-pages' );
	} );

	test( 'rejects anonymous requests', async ( { baseURL } ) => {
		const anonymousContext = await request.newContext( { baseURL } );

		const response = await anonymousContext.get(
			`/wp-json${ ROUTE }`
		);

		expect( response.ok() ).toBeFalsy();
		expect( response.status() ).toBe( 401 );

		await anonymousContext.dispose();
	} );

	test( 'filters results by search term', async ( { requestUtils } ) => {
		await requestUtils.createPost( {
			title: 'Grid Resilience Deep Dive',
			status: 'publish',
		} );
		await requestUtils.createPost( {
			title: 'Completely Unrelated Story',
			status: 'publish',
		} );

		const response = await requestUtils.rest( {
			path: `${ ROUTE }?search=Grid%20Resilience`,
		} );

		expect( response.hasSponsor ).toBe( true );
		expect( response.items ).toHaveLength( 1 );
		expect( response.items[ 0 ].title ).toBe(
			'Grid Resilience Deep Dive'
		);
	} );

	test( 'short-circuits to empty results when the page has no sponsor', async ( {
		requestUtils,
	} ) => {
		await requestUtils.createPost( { title: 'Some Post', status: 'publish' } );

		const page = await requestUtils.rest( {
			path: '/wp/v2/thematic-pages',
			method: 'POST',
			data: { title: 'Unsponsored Thematic Page', status: 'publish' },
		} );

		const response = await requestUtils.rest( {
			path: `${ ROUTE }?sponsored=1&page_id=${ page.id }`,
		} );

		expect( response ).toEqual( { hasSponsor: false, items: [] } );
	} );
} );
