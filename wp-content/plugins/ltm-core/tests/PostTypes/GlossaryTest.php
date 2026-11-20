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

		$box = $wp_meta_boxes['glossary']['acf_after_title']['high']['postexcerpt'];

		$this->assertEmpty( $wp_meta_boxes['glossary']['normal']['core']['postexcerpt'] ?? null );
		$this->assertStringContainsString( 'Short summary', $box['title'] );
		$this->assertStringContainsString( 'acf-required', $box['title'] );
		$this->assertSame( 'render_summary_meta_box', $box['callback'][1] );
	}

	public function test_summary_box_keeps_the_excerpt_input_and_explains_it() {
		$post = self::factory()->post->create_and_get( [ 'post_type' => 'glossary', 'post_excerpt' => 'A <b>summary</b>' ] );

		ob_start();
		( new Glossary() )->render_summary_meta_box( $post );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'name="excerpt" id="excerpt"', $html );
		$this->assertStringContainsString( 'A &lt;b&gt;summary&lt;/b&gt;', $html );
		$this->assertStringContainsString( 'This is the post excerpt', $html );
	}

	/**
	 * @dataProvider summary_validation_cases
	 */
	public function test_summary_is_required_on_glossary_only( $post_type, $excerpt, $expect_error ) {
		acf_reset_validation_errors();
		// A real ACF validation request sets both; acf_request_arg() reads $_REQUEST.
		$_POST['post_type']    = $post_type;
		$_REQUEST['post_type'] = $post_type;
		$_POST['excerpt']      = $excerpt;

		( new Glossary() )->validate_summary();

		$messages = wp_list_pluck( acf_get_validation_errors() ?: [], 'message' );
		$this->assertSame( $expect_error, in_array( 'Short summary is required.', $messages, true ) );

		unset( $_POST['post_type'], $_REQUEST['post_type'], $_POST['excerpt'] );
		acf_reset_validation_errors();
	}

	public function summary_validation_cases() {
		return [
			'glossary, empty'      => [ 'glossary', '', true ],
			'glossary, whitespace' => [ 'glossary', "  \n ", true ],
			'glossary, filled'     => [ 'glossary', 'A summary.', false ],
			'post, empty'          => [ 'post', '', false ],
		];
	}

	public function test_purge_urls_cover_term_index_page_and_old_and_new_related_terms() {
		\LTMCore\Blocks\GlossaryIndex::reset_cache();
		$page    = self::factory()->post->create( [ 'post_type' => 'page', 'post_content' => '<!-- wp:latitudemedia/glossary-index /-->' ] );
		$term    = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$old     = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$new     = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$glossary = new Glossary();

		update_post_meta( $term, 'related_terms', [ (string) $old ] );
		$_POST['acf'] = [ Glossary::RELATED_TERMS_FIELD => [ (string) $new ] ];

		$urls = $glossary->purge_glossary_urls( [ 'https://example.com/already-there/' ], $term );

		unset( $_POST['acf'] );
		\LTMCore\Blocks\GlossaryIndex::reset_cache();

		$this->assertSame(
			[
				'https://example.com/already-there/',
				get_permalink( $term ),
				get_permalink( $page ),
				get_permalink( $old ),
				get_permalink( $new ),
			],
			$urls
		);
	}

	public function test_purge_urls_untouched_for_other_post_types() {
		$post = self::factory()->post->create();

		$this->assertSame( [ 'x' ], ( new Glossary() )->purge_glossary_urls( [ 'x' ], $post ) );
	}

	public function test_get_abbreviation() {
		$term = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$this->assertSame( '', Glossary::get_abbreviation( $term ) );

		update_field( 'field_ltm_glossary_abbreviation', ' BESS ', $term );
		$this->assertSame( 'BESS', Glossary::get_abbreviation( $term ) );
	}

	public function test_field_group_fields() {
		$group = acf_get_field_group( 'group_ltm_glossary_term' );

		$this->assertIsArray( $group, 'Field group group_ltm_glossary_term is not registered.' );
		$this->assertSame( 'glossary', $group['location'][0][0]['value'] );

		$fields = acf_get_fields( $group );
		$this->assertSame(
			[
				[ 'field_ltm_glossary_abbreviation', 'abbreviation', 'text' ],
				[ 'field_ltm_glossary_definition', 'definition', 'wysiwyg' ],
				[ 'field_ltm_glossary_synonyms', 'synonyms', 'repeater' ],
				[ 'field_ltm_glossary_related_terms', 'related_terms', 'relationship' ],
			],
			array_map( static fn( $field ) => [ $field['key'], $field['name'], $field['type'] ], $fields )
		);

		$this->assertSame( 0, $fields[0]['required'] );
		$this->assertNotEmpty( $fields[0]['instructions'] );
		$this->assertSame( 'synonym', $fields[2]['sub_fields'][0]['name'] );
		$this->assertSame( [ 'glossary' ], $fields[3]['post_type'] );
		$this->assertSame( 1, $fields[3]['bidirectional'] );
		$this->assertSame( [ Glossary::RELATED_TERMS_FIELD ], $fields[3]['bidirectional_target'] );
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
