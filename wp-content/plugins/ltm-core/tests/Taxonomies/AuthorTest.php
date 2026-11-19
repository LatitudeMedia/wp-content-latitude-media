<?php
/**
 * Tests for the "Author options" field group, migrated out of the theme. The
 * `author` taxonomy itself belongs to PublishPress Authors.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\Taxonomies;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\Taxonomies\Author
 */
class AuthorTest extends WP_UnitTestCase {

	/**
	 * "Author options" came over from the theme's acf-export.php with its
	 * group key and all field keys intact.
	 */
	public function test_author_options_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_66fbe0fc57909' );

		$this->assertIsArray( $group, 'Field group group_66fbe0fc57909 is not registered.' );
		$this->assertSame( 'Author options', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'taxonomy',
				'operator' => '==',
				'value'    => 'author',
			],
			$group['location'][0][0]
		);

		$this->assertSame(
			[
				[ 'field_66fbe0fc3ae2d', 'author_type', 'select' ],
				[ 'field_66fbe53bed04e', 'linkedin', 'text' ],
				[ 'field_66fbe547ed04f', 'x_twitter', 'text' ],
				[ 'field_67acc3d28e7b8', 'bluesky', 'text' ],
				[ 'field_66fbe5c1ed050', 'other_social', 'text' ],
				[ 'field_66fd24eac1d37', 'linkout', 'text' ],
				[ 'field_66fd25d0c1d38', 'logo', 'image' ],
				[ 'field_67375ad57f5ca', 'bio', 'wysiwyg' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				acf_get_fields( $group )
			)
		);
	}
}
