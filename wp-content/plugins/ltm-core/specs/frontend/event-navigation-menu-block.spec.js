/**
 * Frontend tests for acf/event-navigation-menu-block's server-side render
 * (src/event-navigation-menu-block/render.php) and its view.js behaviour.
 *
 * Primary render coverage for this block -- see
 * specs/frontend/event-description-block.spec.js for why PHPUnit cannot
 * provide it.
 *
 * The block data below mirrors live Event post 16043, including ACF's
 * flattened repeater format (`navigation_links_N_title` / `_navigation_links_N_title`
 * plus a `navigation_links` row count) -- the whole point of the migration is
 * that content already stored in this exact shape keeps resolving.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const LINKS = [
	{ title: 'Overview', anchor: 'overview' },
	{ title: 'Venue', anchor: 'venue' },
	{ title: 'Contact', anchor: 'contactus' },
];

const blockMarkup = ( { display = '1', className } = {} ) => {
	const data = {
		navigation_links: LINKS.length,
		_navigation_links: 'field_693f9e96e2b85',
		button_one: {
			title: 'Register interest',
			url: 'https://example.com/register',
			target: '_blank',
		},
		_button_one: 'field_6940019f6c2cb',
		button_two: '',
		_button_two: 'field_694001bf6c2cc',
		display,
		_display: 'field_693fa5029694a',
	};

	LINKS.forEach( ( { title, anchor }, i ) => {
		data[ `navigation_links_${ i }_title` ] = title;
		data[ `_navigation_links_${ i }_title` ] = 'field_693f9eb4e2b86';
		data[ `navigation_links_${ i }_anchor` ] = anchor;
		data[ `_navigation_links_${ i }_anchor` ] = 'field_693f9eede2b87';
	} );

	const attrs = {
		name: 'acf/event-navigation-menu-block',
		data,
		mode: 'preview',
		...( className ? { className } : {} ),
	};

	return [
		`<!-- wp:acf/event-navigation-menu-block ${ JSON.stringify( attrs ) } /-->`,
		// Scroll targets for the nav links, far enough down to scroll to.
		'<!-- wp:spacer {"height":"1500px"} -->',
		'<div style="height:1500px" aria-hidden="true" class="wp-block-spacer"></div>',
		'<!-- /wp:spacer -->',
		'<!-- wp:heading -->',
		'<h2 id="venue">Venue</h2>',
		'<!-- /wp:heading -->',
	].join( '\n' );
};

test.describe( 'Event navigation menu block frontend', () => {
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

	test( 'renders the links and buttons from the repeater and link fields', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Nav Menu Event',
			blockMarkup( { className: 'pink-theme' } )
		);

		await page.goto( event.link );

		const block = page.locator( '.wp-block-acf-event-navigation-menu-block' );
		await expect( block ).toHaveClass( /content-block/ );
		await expect( block ).toHaveClass( /navigation-menu-section/ );
		await expect( block ).toHaveClass( /pink-theme/ );

		const links = block.locator( '.navigation-menu-links a' );
		await expect( links ).toHaveCount( LINKS.length );
		for ( const [ i, { title, anchor } ] of LINKS.entries() ) {
			await expect( links.nth( i ) ).toHaveText( title );
			await expect( links.nth( i ) ).toHaveAttribute( 'href', `#${ anchor }` );
		}

		const buttons = block.locator( '.buttons-container .nav-button' );
		await expect( buttons ).toHaveCount( 1 );
		await expect( buttons ).toHaveText( 'Register interest' );
		await expect( buttons ).toHaveAttribute( 'href', 'https://example.com/register' );
		await expect( buttons ).toHaveAttribute( 'target', '_blank' );
	} );

	test( 'clicking a nav link scrolls to its anchor and marks it active', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Nav Menu Scroll Event',
			blockMarkup()
		);

		await page.goto( event.link );

		const venueLink = page.locator( '.navigation-menu-links a', { hasText: 'Venue' } );
		await venueLink.click();

		await expect( venueLink ).toHaveClass( /active/ );
		await expect(
			page.locator( '.navigation-menu-links a.active' )
		).toHaveCount( 1 );
		// view.js intercepts the click: no hash navigation, but the page scrolls.
		await expect( page ).not.toHaveURL( /#venue$/ );
		await expect
			.poll( () => page.evaluate( () => window.scrollY ) )
			.toBeGreaterThan( 500 );
	} );

	test( 'renders nothing on the frontend when display is off', async ( {
		page,
		requestUtils,
	} ) => {
		const event = await createEvent(
			requestUtils,
			'Hidden Nav Menu Event',
			blockMarkup( { display: '0' } )
		);

		await page.goto( event.link );

		await expect(
			page.locator( '.wp-block-acf-event-navigation-menu-block' )
		).toHaveCount( 0 );
	} );
} );
