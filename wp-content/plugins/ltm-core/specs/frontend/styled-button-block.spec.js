/**
 * Frontend tests for acf/styled-button-block's server-side render
 * (src/styled-button-block/render.php).
 *
 * Primary render coverage for this block; PHPUnit cannot provide it (see
 * tests/Blocks/StyledButtonTest.php). Each Playwright page load is its own PHP
 * process, so every case here gets a clean ACF block-meta context.
 *
 * Block markup below uses ACF's flattened `fieldname`/`_fieldname` data
 * format, as already stored in live post content -- the whole point of the
 * migration is that existing content keeps resolving.
 *
 * Two behaviours are load-bearing:
 *  - the `cta-button` class, which the THEME styles (.cta-button in
 *    src/assets/scss/base/_global.scss). The block ships no CSS of its own, so
 *    losing that class silently unstyles every button on the site;
 *  - an unconfigured button rendering NOTHING. The theme template printed a
 *    live "Select link" -> "/" anchor to real visitors; the migration made the
 *    placeholder editor-only, and this file is what locks that in.
 *
 * Whole file skips when ACF Pro is absent: CI drops it (see
 * .github/workflows/ltm-core-tests.yml), and without it the block is not
 * registered at all.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const BLOCK = 'acf/styled-button-block';

const blockMarkup = ( { button, anchor } = {} ) => {
	const attrs = {
		name: BLOCK,
		data: {
			button,
			_button: 'field_673b3369c9f60',
		},
		mode: 'preview',
	};

	if ( anchor ) {
		attrs.anchor = anchor;
	}

	return `<!-- wp:acf/styled-button-block ${ JSON.stringify( attrs ) } /-->`;
};

test.describe( 'Styled button block frontend', () => {
	const createdPages = [];

	test.beforeEach( async ( { requestUtils } ) => {
		const registered = await requestUtils
			.rest( { path: `/wp/v2/block-types/${ BLOCK }` } )
			.then( () => true )
			.catch( () => false );
		test.skip(
			! registered,
			`${ BLOCK } is not registered (ACF Pro inactive).`
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

	const createPage = async ( requestUtils, title, content ) => {
		const page = await requestUtils.rest( {
			path: '/wp/v2/pages',
			method: 'POST',
			data: { title, status: 'publish', content },
		} );
		createdPages.push( page.id );
		return page;
	};

	test( 'renders the configured link as a .cta-button anchor', async ( {
		page,
		requestUtils,
	} ) => {
		const created = await createPage(
			requestUtils,
			'Styled Button Configured',
			blockMarkup( {
				button: {
					title: 'Learn more',
					url: 'https://example.com/target',
					target: '',
				},
			} )
		);

		await page.goto( created.link );

		const button = page.locator( 'a.cta-button' );
		await expect( button ).toHaveCount( 1 );
		await expect( button ).toHaveText( 'Learn more' );
		await expect( button ).toHaveAttribute(
			'href',
			'https://example.com/target'
		);

		// get_block_wrapper_attributes() also emits the block's own class.
		await expect( button ).toHaveClass(
			/wp-block-acf-styled-button-block/
		);
	} );

	test( 'carries the link target through', async ( {
		page,
		requestUtils,
	} ) => {
		const created = await createPage(
			requestUtils,
			'Styled Button New Tab',
			blockMarkup( {
				button: {
					title: 'Offsite',
					url: 'https://example.com/offsite',
					target: '_blank',
				},
			} )
		);

		await page.goto( created.link );

		await expect( page.locator( 'a.cta-button' ) ).toHaveAttribute(
			'target',
			'_blank'
		);
	} );

	/**
	 * The theme template never emitted a wrapper at all, so `anchor` support
	 * was declared but dead. render.php now passes it through
	 * get_block_wrapper_attributes().
	 */
	test( 'anchor support emits an id', async ( { page, requestUtils } ) => {
		const created = await createPage(
			requestUtils,
			'Styled Button Anchored',
			blockMarkup( {
				button: { title: 'Jump target', url: '/somewhere', target: '' },
				anchor: 'signup',
			} )
		);

		await page.goto( created.link );

		await expect( page.locator( 'a.cta-button' ) ).toHaveAttribute(
			'id',
			'signup'
		);
	} );

	/**
	 * The migration's one deliberate behaviour change. The theme template fell
	 * back to a live `Select link` -> `/` anchor for real visitors; the
	 * placeholder is now editor-only.
	 */
	test.describe( 'unconfigured button', () => {
		test( 'renders nothing when no link is set at all', async ( {
			page,
			requestUtils,
		} ) => {
			const created = await createPage(
				requestUtils,
				'Styled Button Empty',
				blockMarkup( { button: '' } )
			);

			await page.goto( created.link );

			await expect( page.locator( 'a.cta-button' ) ).toHaveCount( 0 );
			await expect( page.getByText( 'Select link' ) ).toHaveCount( 0 );
		} );

		test( 'renders nothing when the link has a url but no title', async ( {
			page,
			requestUtils,
		} ) => {
			const created = await createPage(
				requestUtils,
				'Styled Button Titleless',
				blockMarkup( {
					button: {
						title: '',
						url: 'https://example.com',
						target: '',
					},
				} )
			);

			await page.goto( created.link );

			await expect( page.locator( 'a.cta-button' ) ).toHaveCount( 0 );
		} );
	} );

	/**
	 * The theme's info-cta-block is NOT migrating, and its InnerBlocks
	 * template still names acf/styled-button-block. Block names resolve
	 * globally, so a button saved inside it must keep rendering now that the
	 * block is served from the plugin.
	 */
	test( "still renders nested inside the theme's info-cta-block", async ( {
		page,
		requestUtils,
	} ) => {
		const created = await createPage(
			requestUtils,
			'Styled Button Inside Info CTA',
			[
				`<!-- wp:acf/info-cta-block ${ JSON.stringify( {
					name: 'acf/info-cta-block',
					data: {
						base_color: '#00B48D',
						_base_color: 'field_674484b91c106',
						shadow_color: '#CCF0E8',
						_shadow_color: 'field_674484c41c107',
						display: '1',
						_display: 'field_673dda0a1e7b7',
					},
					mode: 'preview',
				} ) } -->`,
				'<!-- wp:paragraph --><p>Ready to get started?</p><!-- /wp:paragraph -->',
				blockMarkup( {
					button: {
						title: 'Contact us',
						url: '/contact',
						target: '',
					},
				} ),
				'<!-- /wp:acf/info-cta-block -->',
			].join( '\n' )
		);

		await page.goto( created.link );

		const button = page.locator( 'a.cta-button' );
		await expect( button ).toHaveCount( 1 );
		await expect( button ).toHaveText( 'Contact us' );
	} );
} );
