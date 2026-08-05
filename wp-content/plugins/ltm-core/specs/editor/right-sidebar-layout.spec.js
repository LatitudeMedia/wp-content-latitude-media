/**
 * Smoke spec for the "Right Sidebar Layout" core/group variation.
 *
 * Confirms the variation surfaces in the real inserter (not just that
 * wp.blocks.createBlock() can construct the attributes/innerBlocks by hand),
 * that it produces the expected four-level Group nesting with the
 * "Main Content" / "Sidebar" block-renaming labels, and that the matching
 * class structure renders on the frontend.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Right Sidebar Layout variation', () => {
	test( 'appears in the inserter and creates the expected nested Group structure', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost();

		await editor.showBlockToolbar();
		await page
			.getByRole( 'button', { name: 'Toggle block inserter' } )
			.click();
		await page
			.getByRole( 'searchbox', { name: 'Search' } )
			.fill( 'Right Sidebar Layout' );
		await page
			.getByRole( 'option', { name: 'Right Sidebar Layout' } )
			.click();

		const blocks = await editor.getBlocks();
		const outer = blocks.find( ( block ) =>
			block.attributes.className?.includes( 'right-sidebar-layout' )
		);

		expect( outer ).toBeTruthy();

		const container = outer.innerBlocks[ 0 ];
		expect( container.attributes.className ).toContain( 'container' );

		const wrapper = container.innerBlocks[ 0 ];
		expect( wrapper.attributes.className ).toContain(
			'right-sidebar-layout-wrapper'
		);

		const [ mainColumn, sidebar ] = wrapper.innerBlocks;
		expect( mainColumn.attributes.className ).toContain( 'main-column' );
		expect( mainColumn.attributes.metadata?.name ).toBe( 'Main Content' );
		expect( sidebar.attributes.className ).toContain( 'sidebar' );
		expect( sidebar.attributes.metadata?.name ).toBe( 'Sidebar' );
	} );

	test( 'renders the matching class structure on the frontend', async ( {
		admin,
		editor,
		page,
	} ) => {
		await admin.createNewPost( {
			title: 'Right Sidebar Layout Smoke Test',
		} );

		await editor.insertBlock( {
			name: 'core/group',
			attributes: { className: 'right-sidebar-layout' },
			innerBlocks: [
				{
					name: 'core/group',
					attributes: { className: 'container' },
					innerBlocks: [
						{
							name: 'core/group',
							attributes: {
								className: 'right-sidebar-layout-wrapper',
							},
							innerBlocks: [
								{
									name: 'core/group',
									attributes: { className: 'main-column' },
								},
								{
									name: 'core/group',
									attributes: { className: 'sidebar' },
								},
							],
						},
					],
				},
			],
		} );

		const postId = await editor.publishPost();
		await page.goto( `/?p=${ postId }` );

		await expect(
			page.locator(
				'.right-sidebar-layout .container .right-sidebar-layout-wrapper > .main-column'
			)
		).toBeVisible();
		await expect(
			page.locator(
				'.right-sidebar-layout .container .right-sidebar-layout-wrapper > .sidebar'
			)
		).toBeVisible();
	} );
} );
