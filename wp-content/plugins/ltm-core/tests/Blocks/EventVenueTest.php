<?php
/**
 * Tests for the acf/event-venue-block migration from the theme.
 *
 * Deliberately makes NO render_block() call. ACF Pro's block-meta handling
 * does not tear down cleanly between successive ACF block renders in one
 * PHPUnit process (see EventPreviewBlockRenderTest's docblock), so only the
 * first such render in the suite is trustworthy -- that slot is already taken.
 * Render coverage lives in specs/frontend/event-venue-block.spec.js.
 *
 * The field-key assertions are the point of this file: ACF resolves a block's
 * values by field key, so a mistyped key orphans every existing instance
 * silently -- no error, just empty fields.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\EventVenue
 */
class EventVenueTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_6745c3ee06935' );

		$this->assertIsArray( $group, 'Field group group_6745c3ee06935 is not registered.' );
		$this->assertSame( 'Event venue block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-venue-block',
			],
			$group['location'][0][0]
		);

		$this->assertSame(
			[
				[ 'field_6744a68e8404a', '', 'message' ],
				[ 'field_6745c801bdaff', 'additional_info', 'wysiwyg' ],
				[ 'field_6745c3f2c517f', 'embed_code', 'textarea' ],
				[ 'field_6745c7fcbdafe', 'location_details', 'wysiwyg' ],
				[ 'field_6744a68e8404b', 'display', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				acf_get_fields( $group )
			)
		);
	}

	/**
	 * Registration comes from the committed build/blocks-manifest.php, so this
	 * also catches a build that was never regenerated after block.json changed.
	 * The theme had no styles at all; only one may be the default.
	 */
	public function test_block_registers_with_its_theme_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-venue-block' );

		$this->assertNotNull( $block, 'acf/event-venue-block is not registered.' );

		$this->assertSame(
			[
				[ 'default', true ],
				[ 'pink-theme', false ],
				[ 'blue-theme', false ],
			],
			array_map(
				static fn( $style ) => [ $style['name'], ! empty( $style['isDefault'] ) ],
				(array) $block->styles
			)
		);
	}
}
