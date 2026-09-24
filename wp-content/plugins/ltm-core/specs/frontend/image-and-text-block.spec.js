/**
 * Frontend tests for acf/image-and-text's server-side render
 * (src/image-and-text/render.php).
 *
 * Primary render coverage for this block -- see
 * specs/frontend/event-description-block.spec.js for why PHPUnit cannot
 * provide it, and tests/Blocks/ImageAndTextTest.php for the parts that are
 * assertable without rendering.
 *
 * This file exists because the migration collapsed eight nearly identical
 * theme template partials (template-parts/components/image-and-text/*.php)
 * into one render.php driven by ImageAndText::STYLES. That is a real
 * behaviour risk: a wrong entry in that map produces a structurally valid
 * page that is silently unstyled, or that requests the wrong image size. So
 * every style is rendered here and checked against what its partial emitted.
 */

/**
 * WordPress dependencies
 */
const { test, expect } = require( '@wordpress/e2e-test-utils-playwright' );

const path = require( 'path' );

const LOGO = path.join( __dirname, '..', 'assets', 'image-and-text-logo.png' );

/**
 * What each style's theme partial produced. Mirrors ImageAndText::STYLES --
 * written out literally rather than derived, so that a change to the PHP map
 * has to be made deliberately in both places.
 */
const STYLES = [
	{
		name: 'default',
		classes: [ 'logo-description-block' ],
		container: 'container-narrow',
		wrapper: 'logo-description-block-wrapper',
		imageSlot: 'logo-image',
		textSlot: 'description',
		size: 'image-text-default',
		// `default` alone ignores image_link and emits a bare <img>.
		imageWrapper: null,
		textFirst: false,
	},
	{
		name: 'type2',
		classes: [ 'logo-description-block', 'reverse', 'logo-description-block-bordered' ],
		container: 'container-narrow',
		wrapper: 'logo-description-block-wrapper',
		imageSlot: 'logo-image',
		textSlot: 'description',
		size: 'image-text-default',
		imageWrapper: 'a',
		textFirst: false,
	},
	{
		name: 'type3',
		classes: [ 'icon-text-block' ],
		container: 'container-narrow',
		wrapper: 'icon-text-block-wrapper',
		imageSlot: 'icon-folder',
		textSlot: 'content-folder',
		size: 'image-text-default',
		imageWrapper: 'a',
		textFirst: false,
	},
	{
		name: 'type4',
		classes: [ 'image-text-section' ],
		container: 'container-narrow',
		wrapper: 'image-text-section-wrapper',
		imageSlot: 'image-folder',
		textSlot: 'content-folder',
		size: 'image-text-type4',
		imageWrapper: 'a',
		textFirst: false,
	},
	{
		name: 'type5',
		classes: [ 'podcasts-sponsorship-section' ],
		container: 'container-narrow',
		wrapper: 'podcasts-sponsorship-section-wrapper',
		imageSlot: 'image-folder',
		textSlot: 'content-folder',
		size: 'image-text-type5',
		imageWrapper: 'a',
		textFirst: false,
	},
	{
		name: 'type6',
		// The only style using `.container` rather than `.container-narrow`.
		classes: [ 'image-text-section' ],
		container: 'container',
		wrapper: 'image-text-section-wrapper',
		imageSlot: 'image-folder',
		textSlot: 'content-folder',
		size: 'image-text-type6',
		imageWrapper: 'a',
		textFirst: false,
	},
	{
		name: 'type7',
		classes: [ 'image-text-section', 'tall-it-block' ],
		container: 'container-narrow',
		wrapper: 'image-text-section-wrapper',
		imageSlot: 'image-folder',
		textSlot: 'content-folder',
		size: 'image-text-type7',
		imageWrapper: 'a',
		textFirst: false,
	},
	{
		name: 'type8',
		classes: [ 'image-text-section', 'tall-it-block', 'reverted' ],
		container: 'container-narrow',
		wrapper: 'image-text-section-wrapper',
		imageSlot: 'image-folder',
		textSlot: 'content-folder',
		// Shares type7's size deliberately; there is no image-text-type8.
		size: 'image-text-type7',
		imageWrapper: 'a',
		// The only style that puts the text before the image.
		textFirst: true,
	},
];

const TITLE = 'Image and text heading';
const IMAGE_LINK = 'https://example.com/target';

const blockMarkup = (
	style,
	logoId,
	{ display = '1', imageLink = IMAGE_LINK, title = TITLE, logo = logoId } = {}
) => {
	const attrs = {
		name: 'acf/image-and-text',
		data: {
			title,
			_title: 'field_6735515f7ffa2',
			logo,
			_logo: 'field_673551687ffa3',
			image_link: imageLink,
			_image_link: 'field_674f062c18efb',
			base_color: '#C6168D',
			_base_color: 'field_67362db82424e',
			shadow_color: '#F9E8F4',
			_shadow_color: 'field_6745b76159f4c',
			display,
			_display: 'field_67354f9994828',
		},
		mode: 'preview',
	};

	if ( style ) {
		attrs.className = `is-style-${ style }`;
	}

	return `<!-- wp:acf/image-and-text ${ JSON.stringify(
		attrs
	) } --><!-- wp:paragraph --><p>Body copy for ${ style || 'no style' }.</p><!-- /wp:paragraph --><!-- /wp:acf/image-and-text -->`;
};

test.describe( 'Image and text block frontend', () => {
	const createdPages = [];
	let logoId;

	test.beforeAll( async ( { requestUtils } ) => {
		const media = await requestUtils.uploadMedia( LOGO );
		logoId = media.id;
	} );

	test.afterAll( async ( { requestUtils } ) => {
		if ( logoId ) {
			await requestUtils.rest( {
				path: `/wp/v2/media/${ logoId }`,
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

	test.afterEach( async ( { requestUtils } ) => {
		while ( createdPages.length ) {
			await requestUtils.rest( {
				path: `/wp/v2/pages/${ createdPages.pop() }`,
				method: 'DELETE',
				params: { force: true },
			} );
		}
	} );

	for ( const style of STYLES ) {
		test( `renders the ${ style.name } style with its own markup`, async ( {
			page,
			requestUtils,
		} ) => {
			const created = await createPage(
				requestUtils,
				`Image and text ${ style.name }`,
				blockMarkup( style.name, logoId )
			);

			await page.goto( created.link );

			const block = page.locator( '.wp-block-acf-image-and-text' );
			await expect( block ).toHaveCount( 1 );

			// Wrapper classes, including the shared `content-block`.
			for ( const className of [ 'content-block', ...style.classes ] ) {
				await expect( block ).toHaveClass(
					new RegExp( `(^|\\s)${ className }(\\s|$)` )
				);
			}

			// The colour fields drive CSS custom properties on the wrapper.
			const inlineStyle = await block.getAttribute( 'style' );
			expect( inlineStyle ).toContain( '--custom-block-base-color: #C6168D' );
			expect( inlineStyle ).toContain( '--custom-block-shadow-color: #F9E8F4' );

			// `.container` vs `.container-narrow` -- type6 is the odd one out,
			// and an exact class match is required so `container-narrow` does
			// not satisfy an assertion meant for `container`.
			const container = block.locator( `> .${ style.container }` );
			await expect( container ).toHaveCount( 1 );
			await expect( container ).toHaveAttribute(
				'class',
				new RegExp( `^${ style.container }$` )
			);

			await expect( block.locator( '.bordered-title' ) ).toHaveText( TITLE );

			const wrapper = container.locator( `> .${ style.wrapper }` );
			await expect( wrapper ).toHaveCount( 1 );

			// The requested image size is encoded in the img's own classes, so
			// this catches a size swapped between styles in the map.
			const image = wrapper.locator( `> .${ style.imageSlot } img` );
			await expect( image ).toHaveClass(
				new RegExp( `(^|\\s)size-${ style.size }(\\s|$)` )
			);

			// <a href> when the style honours image_link, <span> when it does
			// not have one set, and a bare <img> for `default`.
			if ( style.imageWrapper ) {
				const link = wrapper.locator(
					`> .${ style.imageSlot } > ${ style.imageWrapper }`
				);
				await expect( link ).toHaveCount( 1 );
				await expect( link ).toHaveAttribute( 'href', IMAGE_LINK );
			} else {
				await expect(
					wrapper.locator( `> .${ style.imageSlot } > img` )
				).toHaveCount( 1 );
			}

			// InnerBlocks content landed in the right slot.
			const text = wrapper.locator( `> .${ style.textSlot }` );
			await expect( text ).toContainText(
				`Body copy for ${ style.name }.`
			);

			// DOM order: type8 alone renders the text first.
			const slotClasses = await wrapper.evaluate( ( el ) =>
				Array.from( el.children ).map( ( child ) => child.className )
			);
			const imageIndex = slotClasses.findIndex( ( c ) =>
				c.split( /\s+/ ).includes( style.imageSlot )
			);
			const textIndex = slotClasses.findIndex( ( c ) =>
				c.split( /\s+/ ).includes( style.textSlot )
			);
			if ( style.textFirst ) {
				expect( textIndex ).toBeLessThan( imageIndex );
			} else {
				expect( imageIndex ).toBeLessThan( textIndex );
			}
		} );
	}

	test( 'falls back to the default style when no style class is set', async ( {
		page,
		requestUtils,
	} ) => {
		const created = await createPage(
			requestUtils,
			'Image and text no style',
			blockMarkup( null, logoId )
		);

		await page.goto( created.link );

		const block = page.locator( '.wp-block-acf-image-and-text' );
		await expect( block ).toHaveClass( /logo-description-block/ );
		await expect(
			block.locator( '.logo-description-block-wrapper > .logo-image' )
		).toHaveCount( 1 );
	} );

	test( 'renders nothing on the front end when Display is off', async ( {
		page,
		requestUtils,
	} ) => {
		const created = await createPage(
			requestUtils,
			'Image and text hidden',
			blockMarkup( 'type4', logoId, { display: '0' } )
		);

		await page.goto( created.link );

		await expect(
			page.locator( '.wp-block-acf-image-and-text' )
		).toHaveCount( 0 );
	} );

	test( 'wraps the image in a span when no image link is set', async ( {
		page,
		requestUtils,
	} ) => {
		const created = await createPage(
			requestUtils,
			'Image and text no link',
			blockMarkup( 'type4', logoId, { imageLink: '' } )
		);

		await page.goto( created.link );

		const slot = page.locator(
			'.wp-block-acf-image-and-text .image-folder'
		);
		await expect( slot.locator( '> span > img' ) ).toHaveCount( 1 );
		await expect( slot.locator( '> a' ) ).toHaveCount( 0 );
	} );

	test( 'omits the image slot without a logo, except for type6 and type8', async ( {
		page,
		requestUtils,
	} ) => {
		// type4 guards the slot; type6 does not, and emits an empty div. Both
		// behaviours are carried over from the theme deliberately.
		for ( const [ style, expectedSlots ] of [
			[ 'type4', 0 ],
			[ 'type6', 1 ],
		] ) {
			const created = await createPage(
				requestUtils,
				`Image and text no logo ${ style }`,
				blockMarkup( style, logoId, { logo: '' } )
			);

			await page.goto( created.link );

			const block = page.locator( '.wp-block-acf-image-and-text' );
			await expect( block ).toHaveCount( 1 );
			await expect( block.locator( '.image-folder' ) ).toHaveCount(
				expectedSlots
			);
			await expect( block.locator( '.image-folder img' ) ).toHaveCount( 0 );
		}
	} );

	test( 'omits the title when it is empty', async ( {
		page,
		requestUtils,
	} ) => {
		const created = await createPage(
			requestUtils,
			'Image and text no title',
			blockMarkup( 'type3', logoId, { title: '' } )
		);

		await page.goto( created.link );

		await expect(
			page.locator( '.wp-block-acf-image-and-text .bordered-title' )
		).toHaveCount( 0 );
	} );
} );
