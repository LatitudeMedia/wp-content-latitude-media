/**
 * Spot-checks LTMCore\Taxonomies\ThematicPageTypes::remove_post_admin_column():
 * the taxonomy is hidden from the Posts list table's column/filter UI, but
 * stays assignable per-post in the editor.
 *
 * Terms in this taxonomy can only be created by the sync code in
 * LTMCore\PostTypes\ThematicPages::sync_taxonomy_term() (one per thematic
 * page, named after it) — manage_terms/edit_terms/delete_terms are all
 * `do_not_allow`, which blocks creating a term through the UI or REST even
 * as an administrator (see tests/Taxonomies/TaxonomiesTest.php). So the
 * "assignable" case below assigns a term produced that way, rather than
 * creating one directly.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Thematic Page Types taxonomy', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts();
		await requestUtils.deleteAllPosts( 'thematic-pages' );
	} );

	test( 'has no column or filter on the Posts list table', async ( {
		admin,
		page,
	} ) => {
		await admin.visitAdminPage( 'edit.php' );

		await expect(
			page.locator( 'th', { hasText: 'Display in Thematic Page' } )
		).toHaveCount( 0 );
		await expect(
			page.locator( '#thematic-page-types' )
		).toHaveCount( 0 );
	} );

	test( 'is assignable in a single post editor and persists on save', async ( {
		admin,
		editor,
		page,
		requestUtils,
	} ) => {
		// The only way a term legitimately exists in this taxonomy: publish a
		// thematic page, whose title the sync code turns into a same-named term.
		await requestUtils.rest( {
			path: '/wp/v2/thematic-pages',
			method: 'POST',
			data: { title: 'Energy Transition', status: 'publish' },
		} );

		await admin.createNewPost( { title: 'Tagged Post' } );
		await editor.openDocumentSettingsSidebar();

		// PanelBody toggle buttons are always visible whether open or closed —
		// checking isVisible() would toggle an already-open panel shut. Only
		// click when aria-expanded is explicitly false.
		const panelButton = page.getByRole( 'button', {
			name: 'Display in Thematic Page',
		} );
		if ( ( await panelButton.getAttribute( 'aria-expanded' ) ) === 'false' ) {
			await panelButton.click();
		}

		await page.getByRole( 'checkbox', { name: 'Energy Transition' } ).check();

		await editor.publishPost();
		await page.reload();

		await expect(
			page.getByRole( 'checkbox', { name: 'Energy Transition' } )
		).toBeChecked();
	} );
} );
