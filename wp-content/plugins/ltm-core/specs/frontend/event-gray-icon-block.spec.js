/**
 * Frontend tests for acf/event-gray-icon-block's server-side render
 * (src/event-gray-icon-block/render.php).
 *
 * Primary render coverage for this block; PHPUnit cannot provide it (see
 * tests/Blocks/EventGrayIconTest.php). Each Playwright page load is its own
 * PHP process, so both layouts and both placements can be exercised for real.
 *
 * Block markup below is copied from live Event posts (2104 for the nested
 * placement, 2131 for the legacy top-level one), including ACF's flattened
 * `fieldname`/`_fieldname` data format — the whole point of the migration is
 * that content already stored in this exact shape keeps resolving.
 *
 * No logo ids are set: attachments don't exist in the test site and the
 * theme's `thumbnail_formatting` action is only reached when one is.
 *
 * Posts are created through /wp/v2/events rather than requestUtils.createPost(),
 * which always targets /wp/v2/posts, and are cleaned up by id because
 * deleteAllPosts() only covers the `post` type.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const grayIconMarkup = ( {
	type = 'default',
	display = '1',
	content,
	content2,
} ) => {
	const data = {
		type,
		_type: 'field_6745f79a85ad8',
		display,
		_display: 'field_6745ed84d47f8',
		content,
		_content: 'field_67463cd4b8eea',
	};
	if ( type === 'type2' ) {
		data.content_2 = content2;
		data._content_2 = 'field_67463d457c479';
	}

	return `<!-- wp:acf/event-gray-icon-block ${ JSON.stringify( {
		name: 'acf/event-gray-icon-block',
		data,
		mode: 'preview',
	} ) } /-->`;
};

const descriptionMarkup = ( innerMarkup ) =>
	[
		'<!-- wp:acf/event-description-block {"name":"acf/event-description-block","data":{"title":"About","_title":"field_674595b6064e9","display":"1","_display":"field_674481518f9b5"},"mode":"preview"} -->',
		'<!-- wp:paragraph --><p>Description copy.</p><!-- /wp:paragraph -->',
		innerMarkup,
		'<!-- /wp:acf/event-description-block -->',
	].join( '\n' );

test.describe( 'Event gray icon block frontend', () => {
	const createdIds = [];

	const createEvent = async ( requestUtils, title, content ) => {
		const event = await requestUtils.rest( {
			path: '/wp/v2/events',
			method: 'POST',
			data: { title, status: 'publish', content },
		} );
		createdIds.push( event.id );
		return event;
	};

	test.afterEach( async ( { requestUtils } ) => {
		while ( createdIds.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/events/${ createdIds.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	test( 'default type renders the single-column layout inside the description block', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Nested Default Gray Icon Event',
			descriptionMarkup(
				grayIconMarkup( { content: '<h3>Who should watch</h3>' } )
			)
		);

		await page.goto( event.link );

		const block = page.locator(
			'.wp-block-acf-event-description-block article .wp-block-acf-event-gray-icon-block'
		);
		await expect( block ).toHaveClass( /content-block/ );
		await expect( block ).toHaveClass( /single-grey-icon-block/ );
		await expect( block ).not.toHaveClass( /half-wide-section/ );
		await expect(
			block.locator( '.container-narrow > .grey-icon-block-wrapper > h3' )
		).toHaveText( 'Who should watch' );
	} );

	test( 'type2 renders two columns', async ( { page, requestUtils } ) => {
		const event = await createEvent(
			requestUtils,
			'Nested Type 2 Gray Icon Event',
			descriptionMarkup(
				grayIconMarkup( {
					type: 'type2',
					content: '<h3>Who should attend?</h3>',
					content2: '<p>Over the course of one day.</p>',
				} )
			)
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-gray-icon-block' );
		await expect( block ).toHaveClass( /half-wide-section/ );
		await expect(
			block.locator(
				'.half-wide-section-wrapper > .half.left > .grey-icon-block > .grey-icon-block-wrapper > h3'
			)
		).toHaveText( 'Who should attend?' );
		await expect(
			block.locator(
				'.half-wide-section-wrapper > .half.right > .grey-icon-block > .grey-icon-block-wrapper > p'
			)
		).toHaveText( 'Over the course of one day.' );
	} );

	test( 'renders nothing on the frontend when display is off', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Hidden Gray Icon Event',
			descriptionMarkup(
				grayIconMarkup( {
					display: '0',
					content: '<p>Should not appear.</p>',
				} )
			)
		);

		await page.goto( event.link );

		await expect(
			page.locator( '.wp-block-acf-event-gray-icon-block' )
		).toHaveCount( 0 );
		await expect( page.getByText( 'Should not appear.' ) ).toHaveCount( 0 );
	} );

	/**
	 * Three live events (2131, 2142, 3963) still place this block at the top
	 * level, as a sibling AFTER the description block. block.json's `parent`
	 * restriction only gates the inserter, so this placement has to keep
	 * rendering exactly as it did in the theme.
	 */
	test.describe( 'legacy top-level placement', () => {
		test( 'both layouts still render as siblings of the description block', async ( {
			page,
			requestUtils,
		} ) => {
			const event = await createEvent(
				requestUtils,
				'Legacy Top Level Gray Icon Event',
				[
					descriptionMarkup( '' ),
					grayIconMarkup( {
						type: 'type2',
						content: '<h3>Who should attend?</h3>',
						content2: '<p>Over the course of one day.</p>',
					} ),
					grayIconMarkup( {
						content: '<h3>Questions facing the power sector</h3>',
					} ),
				].join( '\n' )
			);

			await page.goto( event.link );

			const blocks = page.locator( '.wp-block-acf-event-gray-icon-block' );
			await expect( blocks ).toHaveCount( 2 );

			// Not nested: none of them sit inside the description block.
			await expect(
				page.locator(
					'.wp-block-acf-event-description-block .wp-block-acf-event-gray-icon-block'
				)
			).toHaveCount( 0 );

			// Order preserved: the type2 block immediately follows the description block.
			await expect(
				page.locator(
					'.wp-block-acf-event-description-block + .wp-block-acf-event-gray-icon-block'
				)
			).toHaveClass( /half-wide-section/ );

			await expect(
				blocks.nth( 0 ).locator( '.half.left h3' )
			).toHaveText( 'Who should attend?' );
			await expect(
				blocks.nth( 0 ).locator( '.half.right p' )
			).toHaveText( 'Over the course of one day.' );

			await expect( blocks.nth( 1 ) ).toHaveClass( /single-grey-icon-block/ );
			await expect(
				blocks.nth( 1 ).locator( '.grey-icon-block-wrapper > h3' )
			).toHaveText( 'Questions facing the power sector' );
		} );
	} );
} );
