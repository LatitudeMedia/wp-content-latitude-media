/**
 * Editor tests for acf/image-and-text's curated inner-block list.
 *
 * `allowedBlocks` is an editor-side constraint, so it cannot be asserted from
 * the frontend spec (specs/frontend/image-and-text-block.spec.js) or from
 * PHPUnit. The list lives in src/image-and-text/render.php, passed to ACF's
 * jsx-mode <InnerBlocks> alongside the template.
 *
 * What must hold: the text slot offers headings, paragraphs, lists and the
 * styled button, and nothing else -- and the styled button has to be offered
 * from the PLUGIN now that it no longer exists in the theme.
 *
 * Whole file skips when ACF Pro is absent: CI drops it (see
 * .github/workflows/ltm-core-tests.yml), and without it no acf/* block is
 * registered at all.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const IMAGE_AND_TEXT = 'acf/image-and-text';
const STYLED_BUTTON = 'acf/styled-button-block';

const ALLOWED = [
	'core/heading',
	'core/paragraph',
	'core/list',
	STYLED_BUTTON,
];
const DISALLOWED = [ 'core/image', 'core/quote', 'core/columns' ];

const blockMarkup = [
	`<!-- wp:acf/image-and-text ${ JSON.stringify( {
		name: IMAGE_AND_TEXT,
		data: {
			title: 'Image and text heading',
			_title: 'field_6735515f7ffa2',
			display: '1',
			_display: 'field_67354f9994828',
		},
		mode: 'preview',
	} ) } -->`,
	'<!-- wp:paragraph --><p>Body copy.</p><!-- /wp:paragraph -->',
	'<!-- /wp:acf/image-and-text -->',
].join( '\n' );

test.describe( 'Image and text block editor', () => {
	const createdPages = [];

	test.beforeEach( async ( { requestUtils } ) => {
		const registered = await Promise.all(
			[ IMAGE_AND_TEXT, STYLED_BUTTON ].map( ( name ) =>
				requestUtils
					.rest( { path: `/wp/v2/block-types/${ name }` } )
					.then( () => true )
					.catch( () => false )
			)
		);
		test.skip(
			registered.some( ( ok ) => ! ok ),
			'Image and text or Styled button is not registered (ACF Pro inactive).'
		);
	} );

	test.afterEach( async ( { requestUtils } ) => {
		while ( createdPages.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/pages/${ createdPages.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	test( 'the text slot allows headings, paragraphs, lists and the styled button, and nothing else', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const created = await requestUtils.rest( {
			path: '/wp/v2/pages',
			method: 'POST',
			data: {
				title: 'Image And Text Allowed Blocks',
				status: 'publish',
				content: blockMarkup,
			},
		} );
		createdPages.push( created.id );

		await admin.editPost( created.id );

		// The block itself parsed cleanly, with its seeded paragraph intact.
		const blocks = await editor.getBlocks();
		expect( blocks.map( ( b ) => b.name ) ).toEqual( [ IMAGE_AND_TEXT ] );
		await expect(
			page.getByText(
				'This block contains unexpected or invalid content'
			)
		).toHaveCount( 0 );

		// ACF renders the InnerBlocks area inside its server-rendered preview,
		// so wait for the inner paragraph to exist before asking the store
		// about insertability -- the inner block list does not exist until the
		// preview has been fetched and mounted.
		await expect
			.poll( async () => {
				return page.evaluate( ( name ) => {
					const store = window.wp.data.select( 'core/block-editor' );
					const parent = store
						.getBlocks()
						.find( ( b ) => b.name === name );
					return parent ? parent.innerBlocks.length : 0;
				}, IMAGE_AND_TEXT );
			} )
			.toBeGreaterThan( 0 );

		// Asked of the block editor's own store rather than of an inserter UI,
		// so the answer doesn't depend on which inserter variant is rendered.
		const insertability = await page.evaluate(
			( { parentName, allowed, disallowed } ) => {
				const store = window.wp.data.select( 'core/block-editor' );
				const parentClientId = store
					.getBlocks()
					.find( ( b ) => b.name === parentName ).clientId;

				const check = ( names ) =>
					Object.fromEntries(
						names.map( ( name ) => [
							name,
							store.canInsertBlockType( name, parentClientId ),
						] )
					);

				return {
					...check( allowed ),
					...check( disallowed ),
				};
			},
			{
				parentName: IMAGE_AND_TEXT,
				allowed: ALLOWED,
				disallowed: DISALLOWED,
			}
		);

		expect( insertability ).toEqual( {
			...Object.fromEntries( ALLOWED.map( ( name ) => [ name, true ] ) ),
			...Object.fromEntries(
				DISALLOWED.map( ( name ) => [ name, false ] )
			),
		} );
	} );

	test( 'a styled button added to the text slot saves as an inner block', async ( {
		admin,
		editor,
		requestUtils,
	} ) => {
		const created = await requestUtils.rest( {
			path: '/wp/v2/pages',
			method: 'POST',
			data: {
				title: 'Image And Text With Button',
				status: 'publish',
				content: [
					blockMarkup.replace(
						'<!-- /wp:acf/image-and-text -->',
						[
							`<!-- wp:acf/styled-button-block ${ JSON.stringify(
								{
									name: STYLED_BUTTON,
									data: {
										button: {
											title: 'Learn more',
											url: 'https://example.com/target',
											target: '',
										},
										_button: 'field_673b3369c9f60',
									},
									mode: 'preview',
								}
							) } /-->`,
							'<!-- /wp:acf/image-and-text -->',
						].join( '\n' )
					),
				].join( '\n' ),
			},
		} );
		createdPages.push( created.id );

		await admin.editPost( created.id );

		const blocks = await editor.getBlocks();
		expect( blocks ).toHaveLength( 1 );
		expect( blocks[ 0 ].name ).toBe( IMAGE_AND_TEXT );
		expect( blocks[ 0 ].innerBlocks.map( ( b ) => b.name ) ).toEqual( [
			'core/paragraph',
			STYLED_BUTTON,
		] );
	} );
} );
