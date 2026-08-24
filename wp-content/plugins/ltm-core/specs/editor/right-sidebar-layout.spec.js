/**
 * Smoke spec for the "Right Sidebar Layout" core/columns variation.
 *
 * Confirms the variation surfaces in the real inserter (not just that
 * wp.blocks.createBlock() can construct the attributes/innerBlocks by hand),
 * that it produces a core/columns block with a 66%/33% core/column split
 * carrying the "Main Column" / "Sidebar" block-renaming labels, and that the
 * matching column widths render on the frontend.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Right Sidebar Layout variation', () => {
	test( 'appears in the inserter and creates the expected columns structure', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();

		await editor.showBlockToolbar();
		await page.getByRole( 'button', { name: 'Block Inserter' } ).click();
		await page
			.getByRole( 'searchbox', { name: 'Search' } )
			.fill( 'Right Sidebar Layout' );
		await page
			.getByRole( 'option', { name: 'Right Sidebar Layout' } )
			.click();

		const blocks = await editor.getBlocks();
		const outer = blocks.find(
			( block ) =>
				block.name === 'core/columns' &&
				block.attributes.metadata?.name === 'Right Sidebar Layout'
		);

		expect( outer ).toBeTruthy();
		expect( outer.attributes.isStackedOnMobile ).toBe( true );

		const [ mainColumn, sidebar ] = outer.innerBlocks;
		expect( mainColumn.attributes.width ).toBe( '66%' );
		expect( mainColumn.attributes.metadata?.name ).toBe( 'Main Column' );
		expect( sidebar.attributes.width ).toBe( '33%' );
		expect( sidebar.attributes.metadata?.name ).toBe( 'Sidebar' );
	} );

	test( 'renders the 66/33 column split on the frontend', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost( {
			title: 'Right Sidebar Layout Smoke Test',
		} );

		await editor.insertBlock( {
			name: 'core/columns',
			attributes: {
				isStackedOnMobile: true,
				metadata: { name: 'Right Sidebar Layout' },
			},
			innerBlocks: [
				{
					name: 'core/column',
					attributes: {
						width: '66%',
						metadata: { name: 'Main Column' },
					},
				},
				{
					name: 'core/column',
					attributes: {
						width: '33%',
						metadata: { name: 'Sidebar' },
					},
				},
			],
		} );

		const postId = await editor.publishPost();
		await page.goto( `/?p=${ postId }` );

		const columns = page.locator( '.wp-block-columns > .wp-block-column' );
		await expect( columns ).toHaveCount( 2 );
		await expect( columns.nth( 0 ) ).toHaveCSS( 'flex-basis', '66%' );
		await expect( columns.nth( 1 ) ).toHaveCSS( 'flex-basis', '33%' );
	} );
} );
