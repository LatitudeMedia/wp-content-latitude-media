/**
 * Editor tests for acf/event-gray-icon-block's `parent` restriction and the
 * description block's allowedBlocks.
 *
 * Two things must both be true after the migration:
 *  - the inserter only offers the gray icon block INSIDE a description block;
 *  - the three live events (2131, 2142, 3963) that still carry it at the top
 *    level keep loading in the editor as ordinary, valid blocks.
 *
 * Whole file skips when ACF Pro is absent: CI drops it (see
 * .github/workflows/ltm-core-tests.yml), and without it no acf/* block is
 * registered at all, so there is nothing meaningful to assert.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const GRAY_ICON = 'acf/event-gray-icon-block';
const DESCRIPTION = 'acf/event-description-block';

/**
 * Copied from post 2131: a description block, then the gray icon block twice
 * as top-level siblings (not nested).
 */
const legacyContent = [
	'<!-- wp:acf/event-description-block {"name":"acf/event-description-block","data":{"title":"About","_title":"field_674595b6064e9","display":"1","_display":"field_674481518f9b5"},"mode":"preview"} -->',
	'<!-- wp:paragraph --><p>Intro.</p><!-- /wp:paragraph -->',
	'<!-- /wp:acf/event-description-block -->',
	'<!-- wp:acf/event-gray-icon-block {"name":"acf/event-gray-icon-block","data":{"type":"type2","_type":"field_6745f79a85ad8","display":"1","_display":"field_6745ed84d47f8","content":"<h3>Who should attend?</h3>","_content":"field_67463cd4b8eea","content_2":"<p>Over the course of one day.</p>","_content_2":"field_67463d457c479"},"mode":"edit"} /-->',
	'<!-- wp:acf/event-gray-icon-block {"name":"acf/event-gray-icon-block","data":{"type":"default","_type":"field_6745f79a85ad8","display":"1","_display":"field_6745ed84d47f8","content":"<h3>Questions facing the power sector</h3>","_content":"field_67463cd4b8eea"},"mode":"preview"} /-->',
].join( '\n' );

test.describe( 'Event gray icon block editor', () => {
	const createdIds = [];

	test.beforeEach( async ( { requestUtils } ) => {
		const registered = await requestUtils
			.rest( { path: `/wp/v2/block-types/${ GRAY_ICON }` } )
			.then( () => true )
			.catch( () => false );
		test.skip( ! registered, `${ GRAY_ICON } is not registered (ACF Pro inactive).` );
	} );

	test.afterEach( async ( { requestUtils } ) => {
		while ( createdIds.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/events/${ createdIds.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	test( 'legacy top-level instances still load as valid blocks and the inserter is restricted', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: {
				title: 'Legacy Top Level Gray Icon Event',
				status: 'publish',
				content: legacyContent,
			},
		} );
		createdIds.push( event.id );

		await admin.editPost( event.id );

		// Every block parsed as itself, at the top level, with no recovery
		// prompt: `parent` must not invalidate content that already exists
		// outside the parent.
		const blocks = await editor.getBlocks();
		expect( blocks.map( ( b ) => b.name ) ).toEqual( [
			DESCRIPTION,
			GRAY_ICON,
			GRAY_ICON,
		] );
		await expect(
			page.getByText( 'This block contains unexpected or invalid content' )
		).toHaveCount( 0 );
		await expect( page.getByRole( 'button', { name: 'Attempt recovery' } ) ).toHaveCount( 0 );

		// The restriction itself, asked of the block editor's own store so the
		// answer doesn't depend on which inserter UI variant is rendered.
		const insertability = await page.evaluate(
			( { grayIcon, description } ) => {
				const store = window.wp.data.select( 'core/block-editor' );
				const descriptionClientId = store
					.getBlocks()
					.find( ( b ) => b.name === description ).clientId;
				return {
					grayIconAtRoot: store.canInsertBlockType( grayIcon ),
					grayIconInDescription: store.canInsertBlockType(
						grayIcon,
						descriptionClientId
					),
					paragraphInDescription: store.canInsertBlockType(
						'core/paragraph',
						descriptionClientId
					),
					imageInDescription: store.canInsertBlockType(
						'core/image',
						descriptionClientId
					),
				};
			},
			{ grayIcon: GRAY_ICON, description: DESCRIPTION }
		);

		expect( insertability ).toEqual( {
			grayIconAtRoot: false,
			grayIconInDescription: true,
			paragraphInDescription: true,
			imageInDescription: false,
		} );
	} );
} );
