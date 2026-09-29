<?php
/**
 * Tests for the acf/event-contact-us-block migration from the theme.
 *
 * Deliberately makes NO render_block() call. ACF Pro's block-meta handling
 * does not tear down cleanly between successive ACF block renders in one
 * PHPUnit process (see EventPreviewBlockRenderTest's docblock), so only the
 * first such render in the suite is trustworthy -- that slot is already taken.
 * Everything here is assertable without rendering: that the field group
 * survived the move intact, and that the block registered from the committed
 * build/ manifest with its three colour styles.
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
 * @covers \LTMCore\Blocks\EventContactUs
 */
class EventContactUsTest extends WP_UnitTestCase {

	public function test_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_693f1ae98dd74' );

		$this->assertIsArray( $group, 'Field group group_693f1ae98dd74 is not registered.' );
		$this->assertSame( 'Event contact us block', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'block',
				'operator' => '==',
				'value'    => 'acf/event-contact-us-block',
			],
			$group['location'][0][0]
		);

		$fields = acf_get_fields( $group );

		$this->assertSame(
			[
				[ 'field_693f249f2370a', '', 'message' ],
				[ 'field_693f1ae9a9d04', 'title', 'text' ],
				[ 'field_693f1b0da9d05', 'contact_links', 'repeater' ],
				[ 'field_693f2615b556c', 'display', 'true_false' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$fields
			)
		);
	}

	/**
	 * The repeater's sub-fields carry their own keys, and the stored data keys
	 * them as `contact_links_{row}_{name}` -- so both the keys and the names
	 * have to match the theme's group exactly.
	 */
	public function test_contact_links_sub_field_keys_survived_the_move() {
		$fields = acf_get_fields( acf_get_field_group( 'group_693f1ae98dd74' ) );

		$repeater = current(
			array_filter( $fields, static fn( $field ) => 'contact_links' === $field['name'] )
		);

		$this->assertIsArray( $repeater, 'The contact_links repeater is missing.' );
		$this->assertSame( 'table', $repeater['layout'] );

		$this->assertSame(
			[
				[ 'field_693f1b2473331', 'title', 'text' ],
				[ 'field_693f1b2d73332', 'description', 'wysiwyg' ],
				[ 'field_693f1b4873333', 'cta_link', 'link' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				$repeater['sub_fields']
			)
		);

		$cta_link = end( $repeater['sub_fields'] );

		// render.php reads $card['cta_link']['url'|'target'|'title'], which only
		// exists while the link returns an array rather than a bare URL string.
		$this->assertSame( 'array', $cta_link['return_format'] );
	}

	/**
	 * Registration comes from the committed build/blocks-manifest.php via
	 * LTMCore\Blocks\Title::register_blocks(), so this also catches a build that
	 * was never regenerated after block.json changed.
	 */
	public function test_block_registers_with_its_theme_styles() {
		$block = WP_Block_Type_Registry::get_instance()->get_registered( 'acf/event-contact-us-block' );

		$this->assertNotNull( $block, 'acf/event-contact-us-block is not registered.' );

		$this->assertSame(
			[ 'default', 'pink-theme', 'blue-theme' ],
			array_column( (array) $block->styles, 'name' )
		);
	}
}
