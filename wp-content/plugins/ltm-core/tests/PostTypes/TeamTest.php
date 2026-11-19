<?php
/**
 * Tests for the Team post type, migrated out of the theme along with its
 * "Team member options" field group.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\Team
 */
class TeamTest extends WP_UnitTestCase {

	/**
	 * The post type is registered by the plugin's init hook.
	 */
	public function test_post_type_is_registered() {
		$this->assertTrue( post_type_exists( 'team' ) );
	}

	/**
	 * Same visibility flags the theme registered it with.
	 */
	public function test_post_type_visibility_flags() {
		$post_type = get_post_type_object( 'team' );

		$this->assertTrue( $post_type->public );
		$this->assertTrue( $post_type->show_in_rest );
		$this->assertTrue( $post_type->exclude_from_search );
		$this->assertFalse( $post_type->has_archive );
	}

	/**
	 * "Team member options" came over from the theme's acf-export.php with its
	 * group key and all field keys intact.
	 */
	public function test_team_member_options_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_67050adcdfbbe' );

		$this->assertIsArray( $group, 'Field group group_67050adcdfbbe is not registered.' );
		$this->assertSame( 'Team member options', $group['title'] );
		$this->assertSame(
			[
				'param'    => 'post_type',
				'operator' => '==',
				'value'    => 'team',
			],
			$group['location'][0][0]
		);

		$this->assertSame(
			[
				[ 'field_67050add1481b', 'author', 'taxonomy' ],
				[ 'field_67050b0d1481c', 'job_title', 'text' ],
				[ 'field_67050b311481e', 'linkedin', 'text' ],
				[ 'field_67050b391481f', 'x_twitter', 'text' ],
				[ 'field_67acc3aa3bd5a', 'bluesky', 'text' ],
				[ 'field_67050b4214820', 'other_socials', 'text' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				acf_get_fields( $group )
			)
		);
	}
}
