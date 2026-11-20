/**
 * Frontend tests for latitudemedia/glossary-index: server render
 * (src/glossary-index/render.php) and the view.js live search.
 *
 * Uses only titles and excerpts, so no ACF meta needs seeding.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const TERMS = [ 'Ant', 'Antenna', 'Battery', 'Carbon', 'Zinc' ];

test.describe( 'Glossary index block frontend', () => {
	const createdTerms = [];
	let pageLink;
	let pageId;

	test.beforeAll( async ( { requestUtils } ) => {
		for ( const title of TERMS ) {
			const term = await requestUtils.rest( {
				path: '/wp/v2/glossary',
				method: 'POST',
				data: {
					title,
					excerpt: `Summary of ${ title }`,
					status: 'publish',
				},
			} );
			createdTerms.push( term.id );
		}

		const page = await requestUtils.rest( {
			path: '/wp/v2/pages',
			method: 'POST',
			data: {
				title: 'Glossary index spec',
				status: 'publish',
				content: '<!-- wp:latitudemedia/glossary-index /-->',
			},
		} );
		pageId = page.id;
		pageLink = page.link;
	} );

	test.afterAll( async ( { requestUtils } ) => {
		await requestUtils.rest( {
			path: `/wp/v2/pages/${ pageId }`,
			method: 'DELETE',
			params: { force: true },
		} );
		for ( const id of createdTerms ) {
			await requestUtils.rest( {
				path: `/wp/v2/glossary/${ id }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	const visibleTerms = ( page ) =>
		page
			.locator(
				'.ltm-glossary-index__item:visible .ltm-glossary-index__term'
			)
			.allInnerTexts();

	test( 'narrows the list as the visitor types', async ( { page } ) => {
		await page.goto( pageLink );

		const input = page.getByLabel( 'Search the glossary' );
		await expect( input ).toBeVisible();
		expect( await visibleTerms( page ) ).toEqual( TERMS );

		await input.pressSequentially( 'a' );
		expect( await visibleTerms( page ) ).toEqual( [
			'Ant',
			'Antenna',
			'Battery',
			'Carbon',
		] );
		await expect( page.locator( '#glossary-z' ) ).toBeHidden();
		await expect(
			page.locator( '.ltm-glossary-index__nav a[data-letter="Z"]' )
		).toHaveAttribute( 'aria-disabled', 'true' );

		await input.pressSequentially( 'nt' );
		expect( await visibleTerms( page ) ).toEqual( [ 'Ant', 'Antenna' ] );
		await expect( page.locator( '#glossary-b' ) ).toBeHidden();
		await expect(
			page.locator( '.ltm-glossary-index__status' )
		).toHaveText( '2 terms found' );

		await input.fill( 'xyz' );
		expect( await visibleTerms( page ) ).toEqual( [] );
		await expect( page.locator( '.ltm-glossary-index__empty' ) ).toHaveText(
			'No terms match “xyz”.'
		);

		await input.fill( '' );
		expect( await visibleTerms( page ) ).toEqual( TERMS );
		await expect(
			page.locator( '.ltm-glossary-index__empty' )
		).toBeHidden();
		await expect(
			page.locator( '.ltm-glossary-index__nav a[aria-disabled]' )
		).toHaveCount( 0 );
	} );

	test( 'matching ignores case', async ( { page } ) => {
		await page.goto( pageLink );
		await page.getByLabel( 'Search the glossary' ).fill( 'ZIN' );
		expect( await visibleTerms( page ) ).toEqual( [ 'Zinc' ] );
	} );
} );
