/**
 * Frontend tests for acf/event-description-block's server-side render
 * (src/event-description-block/render.php).
 *
 * Primary render coverage for this block. PHPUnit cannot provide it: ACF Pro's
 * block-meta handling only resolves reliably for the FIRST ACF block render in
 * a process (see tests/Blocks/EventPreviewBlockRenderTest's docblock), and that
 * slot is already taken. Each Playwright page load is its own PHP process, so
 * both layouts can be exercised for real here.
 *
 * The block markup below is copied verbatim from live Event posts (2131 for the
 * plain layout, 2104 for the form layout), including ACF's flattened
 * `fieldname`/`_fieldname` data format — the whole point of the migration is
 * that content already stored in this exact shape keeps resolving.
 *
 * Posts are created through /wp/v2/events rather than requestUtils.createPost(),
 * which always targets /wp/v2/posts, and are cleaned up by id because
 * deleteAllPosts() only covers the `post` type.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const blockMarkup = ( { title, display = '1', className, showForm, body } ) => {
	const attrs = {
		name: 'acf/event-description-block',
		data: {
			title,
			_title: 'field_674595b6064e9',
			display,
			_display: 'field_674481518f9b5',
		},
		mode: 'preview',
		...( className ? { className } : {} ),
		...( showForm === undefined ? {} : { showForm } ),
	};

	return [
		`<!-- wp:acf/event-description-block ${ JSON.stringify( attrs ) } -->`,
		'<!-- wp:paragraph -->',
		`<p>${ body }</p>`,
		'<!-- /wp:paragraph -->',
		'<!-- /wp:acf/event-description-block -->',
	].join( '\n' );
};

test.describe( 'Event description block frontend', () => {
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

	test( 'renders the single-column layout by default', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Single Column Event',
			blockMarkup( {
				title: 'About',
				body: 'Single column description copy.',
			} )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-description-block' );
		await expect( block ).toHaveClass( /content-block/ );
		await expect( block ).toHaveClass( /event-text-section/ );
		await expect( block ).not.toHaveClass( /right-sidebar-layout/ );

		await expect(
			block.locator(
				'.container-narrow > .event-text-section-wrapper > .bordered-title.green'
			)
		).toHaveText( 'About' );
		await expect(
			block.getByText( 'Single column description copy.' )
		).toBeVisible();

		// The sidebar belongs to the form layout only.
		await expect( block.locator( '.form-block' ) ).toHaveCount( 0 );
	} );

	test( 'showForm renders the sidebar layout with the form column', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Show Form Event',
			blockMarkup( {
				title: 'ABOUT',
				showForm: true,
				body: 'Sidebar layout description copy.',
			} )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-description-block' );
		await expect( block ).toHaveClass( /right-sidebar-layout/ );

		// The shared inner fragment is nested one level deeper in this layout.
		await expect(
			block.locator(
				'.right-sidebar-layout-wrapper > .main-column .event-text-section .bordered-title.green'
			)
		).toHaveText( 'ABOUT' );
		await expect(
			block.getByText( 'Sidebar layout description copy.' )
		).toBeVisible();

		await expect(
			block.locator(
				'.sidebar > .form-block.green > .form-block-wrapper'
			)
		).toHaveCount( 1 );
	} );

	/**
	 * The no-migration guarantee. Blocks saved while the form layout was a
	 * block style carry `is-style-type2` and NO showForm attribute, and must
	 * keep rendering the sidebar. Giving showForm a `false` default in
	 * block.json would silently break every one of them -- this is the guard.
	 */
	test( 'legacy is-style-type2 blocks still render the form column', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Legacy Type 2 Event',
			blockMarkup( {
				title: 'ABOUT',
				className: 'is-style-type2',
				body: 'Legacy sidebar layout description copy.',
			} )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-description-block' );
		await expect( block ).toHaveClass( /right-sidebar-layout/ );
		await expect(
			block.locator(
				'.sidebar > .form-block.green > .form-block-wrapper'
			)
		).toHaveCount( 1 );
	} );

	/**
	 * The other side of the tri-state: an explicit false must win over the
	 * legacy class, so an editor can turn the form off on an old post.
	 */
	test( 'showForm false overrides a legacy is-style-type2 class', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Legacy Type 2 Turned Off Event',
			blockMarkup( {
				title: 'ABOUT',
				className: 'is-style-type2',
				showForm: false,
				body: 'Form switched off description copy.',
			} )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-description-block' );
		await expect( block ).toHaveClass( /event-text-section/ );
		await expect( block.locator( '.form-block' ) ).toHaveCount( 0 );
	} );

	test( 'renders nothing on the frontend when display is off', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Hidden Description Event',
			blockMarkup( {
				title: 'About',
				display: '0',
				body: 'Should not appear.',
			} )
		);

		await page.goto( event.link );

		await expect(
			page.locator( '.wp-block-acf-event-description-block' )
		).toHaveCount( 0 );
		await expect( page.getByText( 'Should not appear.' ) ).toHaveCount( 0 );
	} );
} );
