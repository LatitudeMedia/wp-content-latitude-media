/**
 * Tests for the conference-only "Google event details" sidebar section
 * (LTMCore\PostTypes\Events): it and the info alert under Event type appear
 * only while Event type is Conference, and the alert links to the section.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

test.describe( 'Google event details section', () => {
	test.afterEach( async ( { requestUtils } ) => {
		await requestUtils.deleteAllPosts( 'events' );
	} );

	test( 'shows only while Event type is Conference', async ( {
		admin,
		page,
		requestUtils,
	} ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: { title: 'A Conference', status: 'draft' },
		} );

		await admin.editPost( event.id );

		const eventType = page.locator(
			'.acf-field[data-key="field_6713ade8e156a"] select'
		);
		const notice = page.locator(
			'.acf-field[data-key="field_ltm_conference_google_notice"]'
		);
		const section = page.locator( '#acf-group_ltm_google_event' );

		await eventType.selectOption( 'live' );
		await expect( notice ).toBeHidden();
		await expect( section ).toBeHidden();

		await eventType.selectOption( 'conference' );
		await expect( notice ).toBeVisible();
		await expect( section ).toBeVisible();
		await expect(
			notice.getByRole( 'link', { name: 'Google event details' } )
		).toHaveAttribute( 'href', '#acf-group_ltm_google_event' );
		await expect(
			section.getByRole( 'link', {
				name: "Learn more about Google's event structured data",
			} )
		).toHaveAttribute(
			'href',
			'https://developers.google.com/search/docs/appearance/structured-data/event'
		);

		await eventType.selectOption( 'webinar' );
		await expect( notice ).toBeHidden();
		await expect( section ).toBeHidden();
	} );
} );
