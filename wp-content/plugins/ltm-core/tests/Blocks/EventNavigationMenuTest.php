<?php
/**
 * Tests for the acf/event-navigation-menu-block migration from the theme.
 *
 * Deliberately makes NO render_block() call -- see EventDescriptionTest's
 * docblock for why only the first ACF block render per PHPUnit process is
 * trustworthy. Real render coverage lives in
 * specs/frontend/event-navigation-menu-block.spec.js.
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
 * @covers \LTMCore\Blocks\EventNavigationMenu
 */
class EventNavigationMenuTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_693f9e7f697b4' );

		$this->assertIsArray( $group, 'Field group group_693f9e7f697b4 is not registered.' );
		$this->assertSame( 'Event navigation menu', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-navigation-menu-block',
			],
			$group['location'][0][0]
		);

		$fields = acf_get_fields( $group );

		$this->assertSame(
			[
				[ 'field_693f9e7fe2b84', '', 'message' ],
				[ 'field_693f9e96e2b85', 'navigation_links', 'repeater' ],
				[ 'field_6940019f6c2cb', 'button_one', 'link' ],
				[ 'field_694001bf6c2cc', 'button_two', 'link' ],
				[ 'field_693fa5029694a', 'display', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$fields
			)
		);

		// Repeater rows are stored as navigation_links_N_title / _anchor,
		// resolved by these sub-field keys.
		$this->assertSame(
			[
				[ 'field_693f9eb4e2b86', 'title', 'text' ],
				[ 'field_693f9eede2b87', 'anchor', 'text' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$fields[1]['sub_fields']
			)
		);
	}

	public function test_block_is_registered_with_a_view_script() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-navigation-menu-block' );

		$this->assertNotNull( $block, 'acf/event-navigation-menu-block is not registered.' );

		// The smooth-scroll / active-link behaviour moved here from the theme's
		// global custom.js; the block must ship its own front-end script.
		$this->assertNotEmpty( $block->view_script_handles );
	}
}
