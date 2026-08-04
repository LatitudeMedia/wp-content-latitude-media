/**
 * Smoke spec for the latitudemedia/title-block block.
 *
 * Scope is deliberately narrow: prove the harness works end to end (wp-env
 * boots, the plugin's blocks are registered, the block editor is reachable as
 * admin, and the thematic-pages CPT accepts and renders the block). Broader
 * behavioural coverage lands in follow-up specs.
 *
 * Note: the thematic-pages editor does not expose an "Add title" field in the
 * canvas — the Title Block renders the heading itself — so titles are seeded
 * through createNewPost() rather than typed into the editor.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const BLOCK_NAME = 'latitudemedia/title-block';
const KICKER_TEXT = 'Special Report';

test.describe( 'Title Block', () => {
	test( 'is registered and can be inserted into a Thematic Page', async ( {
		admin,
		editor,
	} ) => {
		await admin.createNewPost( { postType: 'thematic-pages' } );

		await editor.insertBlock( { name: BLOCK_NAME } );

		const blocks = await editor.getBlocks();

		expect(
			blocks.some( ( block ) => block.name === BLOCK_NAME )
		).toBeTruthy();
	} );

	test( 'persists the kicker attribute through a save/reload cycle', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost( {
			postType: 'thematic-pages',
			title: 'Title Block Smoke Test',
		} );

		await editor.insertBlock( {
			name: BLOCK_NAME,
			attributes: { kicker: KICKER_TEXT },
		} );

		await editor.publishPost();
		await page.reload();

		// The thematic-pages editor seeds an empty title-block by default, so
		// the inserted block is the *last* match, not the first. Asserting on
		// blocks.find() here would read the empty default and fail.
		const titleBlocks = ( await editor.getBlocks() ).filter(
			( block ) => block.name === BLOCK_NAME
		);

		expect( titleBlocks.length ).toBeGreaterThan( 0 );
		expect(
			titleBlocks.map( ( block ) => block.attributes.kicker )
		).toContain( KICKER_TEXT );
	} );

	/**
	 * block.json declares BOTH a save.js and a render.php. When `render` is
	 * present it takes precedence, so the frontend markup comes from PHP and
	 * the saved markup is only a fallback. This pins that intent: a future
	 * change that drops render.php would silently switch output sources, and
	 * this assertion is what catches it.
	 *
	 * This also covers the wp-env lifecycle setup — the thematic-pages CPT
	 * rewrites to /themes/{slug}, which 404s unless pretty permalinks are set
	 * and rewrite rules flushed (see bin/wp-env-after-start.sh).
	 */
	test( 'renders server-side output on the frontend', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost( {
			postType: 'thematic-pages',
			title: 'Title Block Frontend',
		} );

		await editor.insertBlock( {
			name: BLOCK_NAME,
			attributes: { kicker: KICKER_TEXT },
		} );

		const postId = await editor.publishPost();
		expect( postId ).toBeTruthy();

		await page.goto( `/?p=${ postId }&post_type=thematic-pages` );

		await expect(
			page.locator( '.ltm-title-block' ).getByText( KICKER_TEXT )
		).toBeVisible();
	} );
} );
