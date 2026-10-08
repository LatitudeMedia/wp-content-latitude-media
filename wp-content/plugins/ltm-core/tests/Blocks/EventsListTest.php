<?php
/**
 * Tests for the acf/events-list-block migration from the theme.
 *
 * Deliberately makes NO render_block() call -- see EventDescriptionTest's
 * docblock for why only the first ACF block render per PHPUnit process is
 * trustworthy. Real render coverage lives in
 * specs/frontend/events-list-block.spec.js, which also covers
 * get_events_list()'s upcoming/past split -- that query goes through the theme's
 * Manage_Data(), and the theme is not loaded under PHPUnit.
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
 * @covers \LTMCore\Blocks\EventsList
 */
class EventsListTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_673f9cef9b6de' );

		$this->assertIsArray( $group, 'Field group group_673f9cef9b6de is not registered.' );
		$this->assertSame( 'Events list block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/events-list-block',
			],
			$group['location'][0][0]
		);

		$fields = acf_get_fields( $group );

		$this->assertSame(
			[
				[ 'field_673f9b6dd2721', '', 'message' ],
				[ 'field_673faa95e2af4', 'title', 'text' ],
				[ 'field_673fad446ef02', 'type', 'select' ],
				[ 'field_673f9cf3cdb8e', 'events', 'relationship' ],
				[ 'field_673f9b6dd2722', 'display', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$fields
			)
		);

		// `custom` is unused on the live site but kept, with the relationship
		// field it reveals, so no stored instance can orphan.
		$this->assertSame(
			[ 'upcoming', 'past', 'custom' ],
			array_keys( $fields[2]['choices'] )
		);
	}

	public function test_block_is_registered_for_pages_with_a_view_script() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/events-list-block' );

		$this->assertNotNull( $block, 'acf/events-list-block is not registered.' );

		// The "load more" behaviour moved here from the theme's
		// load-more-events.js; the block must ship its own front-end script.
		$this->assertNotEmpty( $block->view_script_handles );

		$this->assertSame( [ 'page' ], acf_get_block_type( 'acf/events-list-block' )['post_types'] );
	}

	public function test_dead_load_more_rest_route_is_gone() {
		$this->assertArrayNotHasKey( '/wp/v2/events/load-more', rest_get_server()->get_routes() );
	}
}
