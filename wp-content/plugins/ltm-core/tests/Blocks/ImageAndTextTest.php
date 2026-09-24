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
 * committed build/ manifest with all eight styles, and that the style map
 * that replaced the theme's eight template partials resolves correctly.
 * Real render coverage for all eight styles lives in
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

	public function test_block_is_registered_with_all_eight_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/image-and-text' );

		$this->assertNotNull( $block, 'acf/image-and-text is not registered.' );

		$styles = array_map(
			static fn( $style ) => [ $style['name'], ! empty( $style['isDefault'] ) ],
			(array) $block->styles
		);

		// Exactly one default: the theme registration marked ALL EIGHT styles
		// isDefault, which is incoherent (isDefault means "active when no
		// is-style-* class is present"). Fixed during the migration.
		$this->assertSame(
			[
				[ 'default', true ],
				[ 'type2', false ],
				[ 'type3', false ],
				[ 'type4', false ],
				[ 'type5', false ],
				[ 'type6', false ],
				[ 'type7', false ],
				[ 'type8', false ],
			],
			$styles
		);
	}

	/**
	 * The full shape of every style, pinned against the eight theme partials
	 * this map replaced. These strings are what the stylesheets hook onto, so
	 * a typo here is a silently unstyled block on live content.
	 *
	 * @dataProvider provide_styles
	 */
	public function test_style_config_matches_the_theme_partials( string $class_name, array $expected ) {
		$this->assertSame( $expected, ImageAndText::style_config( $class_name ) );
	}

	public static function provide_styles(): array {
		$cases = [];

		foreach ( ImageAndText::STYLES as $name => $config ) {
			$cases[ "is-style-{$name}" ] = [ "is-style-{$name}", $config ];
		}

		// Everything below must fall back to the default style, matching the
		// theme dispatcher, which passed an unresolved name to
		// get_template_part() and so loaded nothing at all for an unknown
		// style. Falling back is strictly better and is what we assert.
		$default = ImageAndText::STYLES['default'];

		$cases['no class at all']          = [ '', $default ];
		$cases['only a theme class']       = [ 'pink-theme', $default ];
		$cases['unknown style']            = [ 'is-style-type99', $default ];
		$cases['substring is not a match'] = [ 'not-is-style-type4', $default ];

		// The theme's ltm_get_block_style() got these wrong: it read
		// $classStyle[0] after an array_filter() that preserves keys (so a
		// style class that is not first was missed) and parsed with
		// end( explode( '-', ... ) ) (so a hyphenated suffix resolved to its
		// last segment).
		$cases['style class not first']    = [ 'pink-theme is-style-type4', ImageAndText::STYLES['type4'] ];
		$cases['extra whitespace']         = [ '  is-style-type6   blue-theme ', ImageAndText::STYLES['type6'] ];
		$cases['hyphenated unknown style'] = [ 'is-style-type4-compact', $default ];

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
		$this->assertFalse( ImageAndText::STYLES['default']['link_image'] );

		// type6 and type8 alone omitted the !empty($logo) guard, so they emit
		// an empty image slot div when no logo is set.
		$this->assertFalse( ImageAndText::STYLES['type6']['guard_image'] );
		$this->assertFalse( ImageAndText::STYLES['type8']['guard_image'] );

		// type8 alone renders the text before the image.
		$this->assertTrue( ImageAndText::STYLES['type8']['text_first'] );
	}
}
