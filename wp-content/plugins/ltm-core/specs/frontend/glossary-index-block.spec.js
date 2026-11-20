/**
 * Frontend tests for latitudemedia/glossary-index: server render
 * (src/glossary-index/render.php) and the view.js live search.
 *
 * Terms are created over REST; Battery's abbreviation and synonyms are ACF
 * fields the REST API doesn't expose, so they're written with WP-CLI through
 * wp-env (as events-list-block.spec.js does for event meta).
 */

/**
 * External dependencies
 */
const { execFileSync } = require( 'child_process' );
const path = require( 'path' );

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const PLUGIN_ROOT = path.join( __dirname, '..', '..' );

/**
 * Gives a term an abbreviation and synonyms via ACF's update_field().
 *
 * @param {number}   id           Glossary term ID.
 * @param {string}   abbreviation Abbreviation.
 * @param {string[]} synonyms     Synonyms.
 */
const setTermFields = ( id, abbreviation, synonyms ) => {
	const payload = Buffer.from(
		JSON.stringify( { id, abbreviation, synonyms } )
	).toString( 'base64' );

	execFileSync(
		'npx',
		[
			'wp-env',
			'run',
			'tests-cli',
			'wp',
			'eval',
			`$d = json_decode( base64_decode( '${ payload }' ), true );
			update_field( 'field_ltm_glossary_abbreviation', $d['abbreviation'], $d['id'] );
			update_field( 'field_ltm_glossary_synonyms', array_map( fn( $s ) => [ 'field_ltm_glossary_synonym' => $s ], $d['synonyms'] ), $d['id'] );`,
		],
		{ cwd: PLUGIN_ROOT, stdio: 'pipe' }
	);
};

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

		setTermFields( createdTerms[ TERMS.indexOf( 'Battery' ) ], 'BESS', [
			'Cell bank',
		] );

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

	// Term names of the visible items, without any "(ABBR)" suffix.
	const visibleTerms = async ( page ) =>
		(
			await page
				.locator(
					'.ltm-glossary-index__item:visible .ltm-glossary-index__term'
				)
				.allInnerTexts()
		).map( ( text ) => text.replace( /\s*\([^)]*\)$/, '' ).trim() );

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

	test( 'matches abbreviations and synonyms', async ( { page } ) => {
		await page.goto( pageLink );
		const input = page.getByLabel( 'Search the glossary' );
		const battery = page.locator( '.ltm-glossary-index__item', {
			hasText: 'Battery',
		} );
		const aka = battery.locator( '.ltm-glossary-index__aka' );

		await expect( battery ).toContainText( '(BESS)' );

		await input.fill( 'bess' );
		expect( await visibleTerms( page ) ).toEqual( [ 'Battery' ] );
		await expect( aka ).toBeHidden();

		await input.fill( 'cell' );
		expect( await visibleTerms( page ) ).toEqual( [ 'Battery' ] );
		await expect( aka ).toBeVisible();
		await expect( aka ).toHaveText( 'Also known as: Cell bank' );

		await input.fill( 'batt' );
		await expect( aka ).toBeHidden();

		await input.fill( '' );
		await expect( aka ).toBeHidden();
	} );

	test( 'matching ignores case', async ( { page } ) => {
		await page.goto( pageLink );
		await page.getByLabel( 'Search the glossary' ).fill( 'ZIN' );
		expect( await visibleTerms( page ) ).toEqual( [ 'Zinc' ] );
	} );
} );
