<?php
/**
 * Tests for the acf/event-description-block migration from the theme.
 *
 * Deliberately makes NO render_block() call. ACF Pro's block-meta handling
 * does not tear down cleanly between successive ACF block renders in one
 * PHPUnit process (see EventPreviewBlockRenderTest's docblock), so only the
 * first such render in the suite is trustworthy -- that slot is already
 * taken. Everything here is assertable without rendering: that the field
 * group survived the move intact, that the block registered from the
 * committed build/ manifest, and that the one piece of genuinely new logic
 * (style selection) behaves. Real render coverage for both styles lives in
 * specs/frontend/event-description-block.spec.js, where each page load is its
 * own PHP process and the ACF constraint does not apply.
 *
 * The field-key assertions are the point of this file: ACF resolves a block's
 * values by field key, so a mistyped key orphans every existing instance
 * silently -- no error, just empty fields.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use LTMCore\Blocks\EventDescription;
use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\EventDescription
 */
class EventDescriptionTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_674595857108f' );

		$this->assertIsArray( $group, 'Field group group_674595857108f is not registered.' );
		$this->assertSame( 'Event description block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-description-block',
			],
			$group['location'][0][0]
		);

		$fields = array_map(
			static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
			acf_get_fields( $group )
		);

		$this->assertSame(
			[
				[ 'field_674481518f9b4', '', 'message' ],
				[ 'field_674595b6064e9', 'title', 'text' ],
				[ 'field_674481518f9b5', 'display', 'true_false' ],
			],
			$fields
		);
	}

	public function test_block_is_registered_with_both_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-description-block' );

		$this->assertNotNull( $block, 'acf/event-description-block is not registered.' );

		$styles = array_map(
			static fn( $style ) => [ $style['name'], ! empty( $style['isDefault'] ) ],
			(array) $block->styles
		);

		// Exactly one default: the theme registration marked BOTH styles
		// isDefault, which is incoherent (isDefault means "active when no
		// is-style-* class is present"). Fixed during the migration.
		$this->assertSame(
			[
				[ 'default', true ],
				[ 'type2', false ],
			],
			$styles
		);
	}

	/**
	 * @dataProvider provide_class_names
	 */
	public function test_is_type2( string $class_name, bool $expected ) {
		$this->assertSame( $expected, EventDescription::is_type2( $class_name ) );
	}

	/**
	 * The first six rows are every className string that actually occurs in
	 * live Event content; the rest guard the ways the theme's
	 * ltm_get_block_style() would have got it wrong.
	 */
	public static function provide_class_names(): array {
		return [
			'live: type2'                 => [ 'is-style-type2', true ],
			'live: none'                  => [ '', false ],
			'live: blue-theme'            => [ 'blue-theme', false ],
			'live: pink-theme'            => [ 'pink-theme', false ],
			'live: pink-separator'        => [ 'pink-separator', false ],
			'live: two theme classes'     => [ 'pink-separator pink-theme', false ],
			'style class not first'       => [ 'blue-theme is-style-type2', true ],
			'extra whitespace'            => [ '  is-style-type2   blue-theme ', true ],
			'longer style name'           => [ 'is-style-type2-compact', false ],
			'different style'             => [ 'is-style-default', false ],
			'substring is not a match'    => [ 'not-is-style-type2', false ],
		];
	}
}
