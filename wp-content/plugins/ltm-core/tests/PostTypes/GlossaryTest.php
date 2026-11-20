<?php
/**
 * Tests for the Glossary post type and its "Glossary term" field group.
 *
 * @package LTMCore
 */

namespace LTMCore\Tests\PostTypes;

use LTMCore\PostTypes\Glossary;
use WP_UnitTestCase;

/**
 * @covers \LTMCore\PostTypes\Glossary
 */
class GlossaryTest extends WP_UnitTestCase {

	public function test_post_type_is_registered() {
		$this->assertTrue( post_type_exists( 'glossary' ) );
	}

	public function test_post_type_args() {
		$post_type = get_post_type_object( 'glossary' );

		$this->assertTrue( $post_type->public );
		$this->assertTrue( $post_type->show_in_rest );
		$this->assertFalse( $post_type->has_archive );
		$this->assertFalse( $post_type->exclude_from_search );
		$this->assertSame( 'glossary', $post_type->rewrite['slug'] );
		$this->assertTrue( post_type_supports( 'glossary', 'title' ) );
		$this->assertTrue( post_type_supports( 'glossary', 'excerpt' ) );
		$this->assertFalse( post_type_supports( 'glossary', 'editor' ) );
	}

	public function test_block_editor_is_disabled_for_glossary_only() {
		$this->assertFalse( use_block_editor_for_post_type( 'glossary' ) );
		$this->assertTrue( use_block_editor_for_post_type( 'post' ) );
	}

	/**
	 * The filter itself holds even if `editor` support is added later.
	 */
	public function test_block_editor_filter_returns_false_for_glossary() {
		$this->assertFalse( apply_filters( 'use_block_editor_for_post_type', true, 'glossary' ) );
		$this->assertTrue( apply_filters( 'use_block_editor_for_post_type', true, 'page' ) );
	}

	/**
	 * The excerpt box becomes "Short summary" in ACF's after-title slot,
	 * registered before ACF's own boxes so it renders above Definition.
	 */
	public function test_excerpt_box_moves_above_the_acf_fields() {
		global $wp_meta_boxes;
		$wp_meta_boxes = [];

		set_current_screen( 'glossary' );
		add_meta_box( 'postexcerpt', 'Excerpt', 'post_excerpt_meta_box', 'glossary', 'normal', 'core' );
		add_meta_box( 'acf-group_ltm_glossary_term', 'Glossary term', '__return_null', 'glossary', 'acf_after_title', 'high' );

		( new Glossary() )->move_excerpt_meta_box( 'glossary' );

		$this->assertEmpty( $wp_meta_boxes['glossary']['normal']['core']['postexcerpt'] ?? null );
		$this->assertSame( 'Short summary', $wp_meta_boxes['glossary']['acf_after_title']['high']['postexcerpt']['title'] );
	}

	public function test_field_group_fields() {
		$group = acf_get_field_group( 'group_ltm_glossary_term' );

		$this->assertIsArray( $group, 'Field group group_ltm_glossary_term is not registered.' );
		$this->assertSame( 'glossary', $group['location'][0][0]['value'] );

		$fields = acf_get_fields( $group );
		$this->assertSame(
			[
				[ 'field_ltm_glossary_definition', 'definition', 'wysiwyg' ],
				[ 'field_ltm_glossary_synonyms', 'synonyms', 'repeater' ],
				[ 'field_ltm_glossary_related_terms', 'related_terms', 'relationship' ],
			],
			array_map( static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ], $fields )
		);

		$this->assertSame( 'synonym', $fields[1]['sub_fields'][0]['name'] );
		$this->assertSame( [ 'glossary' ], $fields[2]['post_type'] );
		$this->assertSame( 1, $fields[2]['bidirectional'] );
		$this->assertSame( [ Glossary::RELATED_TERMS_FIELD ], $fields[2]['bidirectional_target'] );
	}

	public function test_relating_a_term_lists_it_on_the_other_term_too() {
		$ant     = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Ant' ] );
		$antenna = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Antenna' ] );

		update_field( Glossary::RELATED_TERMS_FIELD, [ $antenna ], $ant );

		$this->assertSame( [ $ant ], array_map( 'intval', get_field( 'related_terms', $antenna ) ) );
	}

	public function test_relationship_choices_exclude_the_current_term() {
		$field = acf_get_field( Glossary::RELATED_TERMS_FIELD );
		$args  = apply_filters( 'acf/fields/relationship/query/key=' . Glossary::RELATED_TERMS_FIELD, [ 'post_type' => [ 'glossary' ] ], $field, 42 );

		$this->assertContains( 42, $args['post__not_in'] );
	}

	public function test_get_synonyms() {
		$term = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		update_field(
			'field_ltm_glossary_synonyms',
			[
				[ 'field_ltm_glossary_synonym' => 'BESS' ],
				[ 'field_ltm_glossary_synonym' => ' Cell ' ],
			],
			$term
		);

		$this->assertSame( [ 'BESS', 'Cell' ], Glossary::get_synonyms( $term ) );
		$this->assertSame( [], Glossary::get_synonyms( self::factory()->post->create( [ 'post_type' => 'glossary' ] ) ) );
	}

	public function test_get_related_term_ids_skips_unpublished_terms() {
		$term      = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$published = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$draft     = self::factory()->post->create( [ 'post_type' => 'glossary' ] );

		update_field( Glossary::RELATED_TERMS_FIELD, [ $published, $draft ], $term );
		wp_update_post( [ 'ID' => $draft, 'post_status' => 'draft' ] );

		$this->assertSame( [ $published ], Glossary::get_related_term_ids( $term ) );
	}
}
