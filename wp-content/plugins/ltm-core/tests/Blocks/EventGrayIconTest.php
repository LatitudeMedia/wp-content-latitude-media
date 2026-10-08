<?php
/**
 * Tests for the acf/event-gray-icon-block migration from the theme.
 *
 * Deliberately makes NO render_block() call -- ACF Pro's block-meta handling
 * only resolves reliably for the FIRST ACF block render in a PHPUnit process
 * (see EventPreviewBlockRenderTest's docblock), and that slot is taken. Render
 * coverage lives in specs/frontend/event-gray-icon-block.spec.js.
 *
 * The field-key assertions are the point: ACF resolves a block's values by
 * field key, so a mistyped key orphans every existing instance silently.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Blocks;

use WP_Block_Type_Registry;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\Blocks\EventGrayIcon
 */
class EventGrayIconTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_6745f79613c6a' );

		$this->assertIsArray( $group, 'Field group group_6745f79613c6a is not registered.' );
		$this->assertSame( 'Event gray icon block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-gray-icon-block',
			],
			$group['location'][0][0]
		);

		$fields = array_map(
			static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
			acf_get_fields( $group )
		);

		$this->assertSame(
			[
				[ 'field_6745ed84d47f7', '', 'message' ],
				[ 'field_6746402d35366', '', 'tab' ],
				[ 'field_6745f79a85ad8', 'type', 'select' ],
				[ 'field_6745ed84d47f8', 'display', 'true_false' ],
				[ 'field_67463f2a9524f', '', 'tab' ],
				[ 'field_6745f7bf729c4', 'logo', 'image' ],
				[ 'field_67463cd4b8eea', 'content', 'wysiwyg' ],
				[ 'field_67463f139524e', '', 'tab' ],
				[ 'field_6745f7d9729c5', 'logo_2', 'image' ],
				[ 'field_67463d457c479', 'content_2', 'wysiwyg' ],
			],
			$fields
		);
	}

	public function test_block_is_registered_as_a_child_of_the_description_block() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-gray-icon-block' );

		$this->assertNotNull( $block, 'acf/event-gray-icon-block is not registered.' );
		$this->assertSame( [ 'acf/event-description-block' ], $block->parent );
	}

	/**
	 * Three live Event posts (2131, 2142, 3963) still carry this block at the
	 * TOP level, next to the description block rather than inside it. The
	 * `parent` restriction only gates the inserter, so that content must keep
	 * parsing as this block and its flattened `_fieldname` keys must still map
	 * onto the migrated field group -- otherwise those pages render empty.
	 *
	 * Fixture copied from post 2131's block sequence, content trimmed.
	 */
	public function test_legacy_top_level_content_still_maps_onto_the_field_group() {
		$content = implode(
			"\n",
			[
				'<!-- wp:acf/event-description-block {"name":"acf/event-description-block","data":{"title":"About","_title":"field_674595b6064e9","display":"1","_display":"field_674481518f9b5"},"mode":"preview"} -->',
				'<!-- wp:paragraph --><p>Intro.</p><!-- /wp:paragraph -->',
				'<!-- /wp:acf/event-description-block -->',
				'<!-- wp:acf/event-gray-icon-block {"name":"acf/event-gray-icon-block","data":{"type":"type2","_type":"field_6745f79a85ad8","display":"1","_display":"field_6745ed84d47f8","logo":1988,"_logo":"field_6745f7bf729c4","content":"<h3>Who should attend?</h3>","_content":"field_67463cd4b8eea","logo_2":1986,"_logo_2":"field_6745f7d9729c5","content_2":"<p>Over the course of one day.</p>","_content_2":"field_67463d457c479"},"mode":"edit"} /-->',
				'<!-- wp:acf/event-gray-icon-block {"name":"acf/event-gray-icon-block","data":{"type":"default","_type":"field_6745f79a85ad8","display":"1","_display":"field_6745ed84d47f8","logo":1985,"_logo":"field_6745f7bf729c4","content":"<h3>Questions facing the power sector</h3>","_content":"field_67463cd4b8eea"},"mode":"preview"} /-->',
			]
		);

		$blocks    = array_values( array_filter( parse_blocks( $content ), static fn( $b ) => null !== $b['blockName'] ) );
		$top_level = array_map( static fn( $b ) => $b['blockName'], $blocks );

		$this->assertSame(
			[ 'acf/event-description-block', 'acf/event-gray-icon-block', 'acf/event-gray-icon-block' ],
			$top_level
		);
		$this->assertSame( [], $blocks[1]['innerBlocks'], 'Legacy gray icon blocks are self-closing, not nested.' );

		foreach ( array_slice( $blocks, 1 ) as $block ) {
			foreach ( $block['attrs']['data'] as $key => $value ) {
				if ( '_' !== $key[0] ) {
					continue;
				}
				$field = acf_get_field( $value );

				$this->assertIsArray( $field, "Legacy key {$value} no longer resolves to a field." );
				$this->assertSame( substr( $key, 1 ), $field['name'], "Legacy key {$value} maps to the wrong field name." );
			}
		}
	}
}
