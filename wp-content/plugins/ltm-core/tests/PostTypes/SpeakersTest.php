<?php
/**
 * Tests for the Speakers post type, migrated out of the theme along with its
 * "Speaker options" field group and its two image sizes.
 *
 * The key/name assertions are the point of this file. ACF resolves values by
 * field key and WordPress records generated thumbnails against the image size
 * *name*, so a mistyped key or a renamed size orphans existing data silently --
 * no error, just empty fields or regenerated-from-scratch images.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\Speakers
 */
class SpeakersTest extends WP_UnitTestCase {

	/**
	 * The post type is registered by the plugin's init hook.
	 */
	public function test_post_type_is_registered() {
		$this->assertTrue( post_type_exists( 'speakers' ) );
	}

	/**
	 * The post type is exposed to the block editor / REST API but kept out of
	 * front-end search results, same as events and thematic-pages. REST matters
	 * in particular: specs/frontend/event-speakers-block.spec.js creates its
	 * fixtures through /wp/v2/speakers.
	 */
	public function test_post_type_visibility_flags() {
		$post_type = get_post_type_object( 'speakers' );

		$this->assertTrue( $post_type->public );
		$this->assertTrue( $post_type->show_in_rest );
		$this->assertTrue( $post_type->exclude_from_search );
		$this->assertFalse( $post_type->has_archive );
	}

	/**
	 * The editor needs a title, the blocks need a thumbnail, and the speaker bio
	 * rendered in the modal comes from post content.
	 */
	public function test_post_type_supports_what_the_blocks_render() {
		foreach ( [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ] as $feature ) {
			$this->assertTrue(
				post_type_supports( 'speakers', $feature ),
				"speakers should support '{$feature}'."
			);
		}
	}

	/**
	 * "Speaker options" came over from the theme's acf-export.php with its group
	 * key and all three field keys intact.
	 */
	public function test_speaker_options_field_group_keys_survived_the_move() {
		$group = acf_get_field_group( 'group_67050ea568064' );

		$this->assertIsArray( $group, 'Field group group_67050ea568064 is not registered.' );
		$this->assertSame( 'Speaker options', $group['title'] );
		$this->assertSame( 'side', $group['position'] );
		$this->assertSame(
			[
				'param'    => 'post_type',
				'operator' => '==',
				'value'    => 'speakers',
			],
			$group['location'][0][0]
		);

		$this->assertSame(
			[
				[ 'field_67050ea565c46', 'job_title', 'text' ],
				[ 'field_67050ec665c47', 'company', 'text' ],
				[ 'field_67050ecd65c48', 'linkedin_link', 'text' ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ],
				acf_get_fields( $group )
			)
		);
	}

	/**
	 * The group is scoped to speakers only. The theme's "Team member options"
	 * group has its own `job_title` field under a different key, so a location
	 * rule that leaked onto other post types would show duplicate fields.
	 */
	public function test_speaker_options_only_applies_to_speakers() {
		$keys_for = static fn( string $post_type ) => array_column(
			acf_get_field_groups( [ 'post_type' => $post_type ] ),
			'key'
		);

		$this->assertContains( 'group_67050ea568064', $keys_for( 'speakers' ) );
		$this->assertNotContains( 'group_67050ea568064', $keys_for( 'team' ) );
		$this->assertNotContains( 'group_67050ea568064', $keys_for( 'post' ) );
	}

	/**
	 * Both image sizes moved over at their original names and dimensions.
	 *
	 * 'event-speakers-list' is consumed by the *theme's* our-team-block and
	 * authors-list-block as well as this plugin's event-speakers-block, so the
	 * plugin must keep registering it even though the name suggests otherwise.
	 */
	public function test_speaker_image_sizes_are_registered_unchanged() {
		$sizes = wp_get_additional_image_sizes();

		$this->assertArrayHasKey( 'event-speakers-list', $sizes );
		$this->assertArrayHasKey( 'event-speakers-modal', $sizes );

		$this->assertSame(
			[
				'width'  => 230,
				'height' => 230,
				'crop'   => true,
			],
			$sizes['event-speakers-list']
		);
		$this->assertSame(
			[
				'width'  => 200,
				'height' => 200,
				'crop'   => true,
			],
			$sizes['event-speakers-modal']
		);
	}

	/**
	 * Values written against the migrated field keys read back through the field
	 * *names* the two block renders actually call get_field() with.
	 */
	public function test_speaker_fields_round_trip_through_the_migrated_group() {
		if ( ! function_exists( 'update_field' ) ) {
			$this->markTestSkipped( 'ACF is not active in this environment.' );
		}

		$speaker_id = self::factory()->post->create(
			[
				'post_type'  => 'speakers',
				'post_title' => 'Ada Lovelace',
			]
		);

		update_field( 'field_67050ea565c46', 'Chief Engineer', $speaker_id );
		update_field( 'field_67050ec665c47', 'Analytical Engines Ltd', $speaker_id );
		update_field( 'field_67050ecd65c48', 'https://example.com/in/ada', $speaker_id );

		$this->assertSame( 'Chief Engineer', get_field( 'job_title', $speaker_id ) );
		$this->assertSame( 'Analytical Engines Ltd', get_field( 'company', $speaker_id ) );
		$this->assertSame( 'https://example.com/in/ada', get_field( 'linkedin_link', $speaker_id ) );
	}
}
