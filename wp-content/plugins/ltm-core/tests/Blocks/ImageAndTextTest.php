<?php
/**
 * Tests for the acf/image-and-text migration from the theme.
 *
 * Deliberately makes NO render_block() call. ACF Pro's block-meta handling
 * does not tear down cleanly between successive ACF block renders in one
 * PHPUnit process (see EventPreviewBlockRenderTest's docblock), so only the
 * first such render in the suite is trustworthy -- that slot is already
 * taken. Everything here is assertable without rendering: that the field
 * group survived the move intact, that the block registered from the
 * committed build/ manifest with the `layout` attribute, and that the layout
 * map that replaced the theme's eight template partials resolves correctly.
 * Real render coverage for all eight layouts lives in
 * specs/frontend/image-and-text-block.spec.js, where each page load is its
 * own PHP process and the ACF constraint does not apply.
 *
 * The field-key assertions are the point of this file: ACF resolves a block's
 * values by field key, so a mistyped key orphans every existing instance
 * silently -- no error, just empty fields.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use LTMCore\Blocks\ImageAndText;
use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\ImageAndText
 */
class ImageAndTextTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_6735515080ec9' );

		$this->assertIsArray( $group, 'Field group group_6735515080ec9 is not registered.' );
		$this->assertSame( 'Image and text', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/image-and-text',
			],
			$group['location'][0][0]
		);

		$fields = array_map(
			static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
			acf_get_fields( $group )
		);

		$this->assertSame(
			[
				[ 'field_67354f9994827', '', 'message' ],
				[ 'field_6735515f7ffa2', 'title', 'text' ],
				[ 'field_673551687ffa3', 'logo', 'image' ],
				[ 'field_674f062c18efb', 'image_link', 'text' ],
				[ 'field_67362db82424e', 'base_color', 'color_picker' ],
				[ 'field_6745b76159f4c', 'shadow_color', 'color_picker' ],
				[ 'field_67354f9994828', 'display', 'true_false' ],
			],
			$fields
		);
	}

	/**
	 * render.php feeds $logo['ID'] straight to wp_get_attachment_image(), so
	 * the array return format is load-bearing, not incidental.
	 */
	public function test_logo_field_still_returns_an_array() {
		$field = acf_get_field( 'field_673551687ffa3' );

		$this->assertIsArray( $field, 'Field field_673551687ffa3 is not registered.' );
		$this->assertSame( 'array', $field['return_format'] );
	}

	/**
	 * Layout is a structural choice, not a skin, so it is the `layout`
	 * attribute rather than eight block styles -- which also frees the one
	 * mutually-exclusive is-style-* slot for an actual style later.
	 *
	 * `layout` having no default is load-bearing rather than an oversight: it
	 * is what makes an absent attribute mean "saved before the dropdown
	 * existed" and so lets legacy is-style-typeN blocks keep their layout with
	 * no data migration. Asserted explicitly so a well-meaning
	 * `"default": "default"` in block.json fails here instead of silently on
	 * live content.
	 */
	public function test_block_registers_the_layout_attribute_and_no_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/image-and-text' );

		$this->assertNotNull( $block, 'acf/image-and-text is not registered.' );

		$this->assertSame(
			[],
			(array) $block->styles,
			'The eight layouts are the layout attribute now; no block styles should be registered.'
		);

		$this->assertArrayHasKey( 'layout', (array) $block->attributes );
		$this->assertSame( 'string', $block->attributes['layout']['type'] );
		$this->assertArrayNotHasKey(
			'default',
			$block->attributes['layout'],
			'layout must not have a default; see ImageAndText::layout_key().'
		);
	}

	/**
	 * The full shape of every layout, pinned against the eight theme partials
	 * this map replaced. These strings are what the stylesheets hook onto, so
	 * a typo here is a silently unstyled block on live content.
	 *
	 * @dataProvider provide_blocks
	 */
	public function test_layout_config_matches_the_theme_partials( array $block, array $expected ) {
		$this->assertSame( $expected, ImageAndText::layout_config( $block ) );
	}

	/**
	 * The `layout` rows are the current mechanism; the className rows are
	 * legacy content saved under the retired block styles, which is real on
	 * every environment (and comes back whenever a post revision is restored),
	 * plus guards for the ways the theme's ltm_get_block_style() would have
	 * got it wrong.
	 */
	public static function provide_blocks(): array {
		$cases   = [];
		$default = ImageAndText::LAYOUTS['default'];

		foreach ( ImageAndText::LAYOUTS as $name => $config ) {
			$cases[ "layout: {$name}" ]   = [ [ 'layout' => $name ], $config ];
			$cases[ "legacy: {$name}" ]   = [ [ 'className' => "is-style-{$name}" ], $config ];
		}

		// The attribute wins over a className the block may still carry from
		// before the dropdown existed.
		$cases['layout beats legacy class'] = [
			[ 'layout' => 'type6', 'className' => 'is-style-type3' ],
			ImageAndText::LAYOUTS['type6'],
		];

		// Never touched -- null and absent both mean "fall back to the class".
		$cases['layout null falls back']    = [
			[ 'layout' => null, 'className' => 'is-style-type3' ],
			ImageAndText::LAYOUTS['type3'],
		];
		$cases['unknown layout falls back'] = [
			[ 'layout' => 'type99', 'className' => 'is-style-type3' ],
			ImageAndText::LAYOUTS['type3'],
		];

		// Everything below must fall back to the default layout, matching the
		// theme dispatcher, which passed an unresolved name to
		// get_template_part() and so loaded nothing at all for an unknown
		// style. Falling back is strictly better and is what we assert.
		$cases['no attributes at all']     = [ [], $default ];
		$cases['no class at all']          = [ [ 'className' => '' ], $default ];
		$cases['only a theme class']       = [ [ 'className' => 'pink-theme' ], $default ];
		$cases['unknown style']            = [ [ 'className' => 'is-style-type99' ], $default ];
		$cases['substring is not a match'] = [ [ 'className' => 'not-is-style-type4' ], $default ];

		// The theme's ltm_get_block_style() got these wrong: it read
		// $classStyle[0] after an array_filter() that preserves keys (so a
		// style class that is not first was missed) and parsed with
		// end( explode( '-', ... ) ) (so a hyphenated suffix resolved to its
		// last segment).
		$cases['style class not first']    = [ [ 'className' => 'pink-theme is-style-type4' ], ImageAndText::LAYOUTS['type4'] ];
		$cases['extra whitespace']         = [ [ 'className' => '  is-style-type6   blue-theme ' ], ImageAndText::LAYOUTS['type6'] ];
		$cases['hyphenated unknown style'] = [ [ 'className' => 'is-style-type4-compact' ], $default ];

		return $cases;
	}

	/**
	 * Guards the two quirks carried over from the theme on purpose, so that
	 * "fixing" either one has to be a deliberate edit to this test.
	 */
	public function test_preserved_theme_quirks() {
		// `default` alone rendered a bare <img> via thumbnail_formatting()
		// instead of the print_image_and_text_image() helper, so it ignores
		// the image_link field entirely.
		$this->assertFalse( ImageAndText::LAYOUTS['default']['link_image'] );

		// type6 and type8 alone omitted the !empty($logo) guard, so they emit
		// an empty image slot div when no logo is set.
		$this->assertFalse( ImageAndText::LAYOUTS['type6']['guard_image'] );
		$this->assertFalse( ImageAndText::LAYOUTS['type8']['guard_image'] );

		// type8 alone renders the text before the image.
		$this->assertTrue( ImageAndText::LAYOUTS['type8']['text_first'] );
	}

	/**
	 * render.php emits `image-and-text-{key}` as the hook for layout-specific
	 * CSS (style.scss and the theme's pages/_events.scss both target type4),
	 * so the key a legacy block resolves to is itself front-end behaviour.
	 */
	public function test_layout_key_resolves_legacy_and_current_content() {
		$this->assertSame( 'type4', ImageAndText::layout_key( [ 'layout' => 'type4' ] ) );
		$this->assertSame( 'type4', ImageAndText::layout_key( [ 'className' => 'is-style-type4' ] ) );
		$this->assertSame( 'default', ImageAndText::layout_key( [] ) );
	}
}
