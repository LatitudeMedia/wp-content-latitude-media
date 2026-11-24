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
		$page = self::factory()->post->create( [ 'post_type' => 'page' ] );

		$this->assertSame( [ 'x' ], ( new Glossary() )->purge_glossary_urls( [ 'x' ], $page ) );
	}

	public function test_purge_urls_for_a_term_include_old_and_new_subject_and_mention_posts() {
		$term     = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$old_subj = self::factory()->post->create();
		$new_subj = self::factory()->post->create( [ 'post_type' => 'events' ] );
		$old_ment = self::factory()->post->create( [ 'post_type' => 'podcasts' ] );
		$new_ment = self::factory()->post->create( [ 'post_type' => 'resources' ] );

		update_post_meta( $term, 'subject_of', [ (string) $old_subj ] );
		update_post_meta( $term, 'mentioned_in', [ (string) $old_ment ] );
		$_POST['acf'] = [
			Glossary::SUBJECT_OF_FIELD   => [ (string) $new_subj ],
			Glossary::MENTIONED_IN_FIELD => [ (string) $new_ment ],
		];
		$urls = ( new Glossary() )->purge_glossary_urls( [], $term );
		unset( $_POST['acf'] );

		foreach ( [ $old_subj, $new_subj, $old_ment, $new_ment ] as $id ) {
			$this->assertContains( get_permalink( $id ), $urls );
		}
	}

	/**
	 * Term pages list the posts that are about or mention them, so both
	 * kinds of linked term pages are purged.
	 */
	public function test_purge_urls_for_a_linked_post_include_old_and_new_subject_and_mention_terms() {
		$post      = self::factory()->post->create();
		$old       = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$new       = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$mentioned = self::factory()->post->create( [ 'post_type' => 'glossary' ] );

		update_post_meta( $post, 'glossary_about', [ (string) $old ] );
		$_POST['acf'] = [
			Glossary::POST_ABOUT_FIELD    => [ (string) $new ],
			Glossary::POST_MENTIONS_FIELD => [ (string) $mentioned ],
		];
		$urls = ( new Glossary() )->purge_glossary_urls( [ 'x' ], $post );
		unset( $_POST['acf'] );

		$this->assertSame( [ 'x', get_permalink( $old ), get_permalink( $new ), get_permalink( $mentioned ) ], $urls );
	}

	public function test_post_terms_field_group_sits_in_the_sidebar_of_every_linkable_type() {
		$group = acf_get_field_group( 'group_ltm_glossary_post_terms' );

		$this->assertIsArray( $group );
		$this->assertSame( 'side', $group['position'] );
		$this->assertSame( [ 'post', 'podcasts', 'industry-news', 'resources', 'events' ], Glossary::LINKED_POST_TYPES );
		$this->assertSame(
			Glossary::LINKED_POST_TYPES,
			array_map( static fn( $rule ) => $rule[0]['value'], $group['location'] )
		);

		$this->assertSame(
			[
				[ Glossary::POST_ABOUT_FIELD, 'glossary_about', 'post_object', [ Glossary::SUBJECT_OF_FIELD ] ],
				[ Glossary::POST_MENTIONS_FIELD, 'glossary_mentions', 'post_object', [ Glossary::MENTIONED_IN_FIELD ] ],
			],
			array_map(
				static fn( $field ) => [ $field['key'], $field['name'], $field['type'], $field['bidirectional_target'] ],
				acf_get_fields( $group )
			)
		);
		$this->assertSame( [ Glossary::POST_ABOUT_FIELD ], acf_get_field( Glossary::SUBJECT_OF_FIELD )['bidirectional_target'] );
		$this->assertSame( [ Glossary::POST_MENTIONS_FIELD ], acf_get_field( Glossary::MENTIONED_IN_FIELD )['bidirectional_target'] );
	}

	public function test_subject_and_mention_links_stay_in_sync_from_either_side() {
		$term  = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$post  = self::factory()->post->create();
		$event = self::factory()->post->create( [ 'post_type' => 'events' ] );

		update_field( Glossary::POST_ABOUT_FIELD, [ $term ], $post );
		$this->assertSame( [ $post ], Glossary::get_subject_of_ids( $term ), 'Post "about" -> term "subject of".' );

		update_field( Glossary::MENTIONED_IN_FIELD, [ $event ], $term );
		$this->assertSame( [ 'about' => [], 'mentions' => [ $term ] ], Glossary::get_post_terms( $event ), 'Term "mentioned in" -> post "mentions".' );

		update_field( Glossary::SUBJECT_OF_FIELD, [], $term );
		$this->assertSame( [], Glossary::get_post_terms( $post )['about'], 'Unlinking on the term side removes it from the post.' );
	}

	public function test_get_subject_of_ids_is_newest_first_and_published() {
		$term   = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$older  = self::factory()->post->create( [ 'post_date' => '2026-01-01 00:00:00' ] );
		$newer  = self::factory()->post->create( [ 'post_type' => 'podcasts', 'post_date' => '2026-06-01 00:00:00' ] );
		$draft  = self::factory()->post->create( [ 'post_status' => 'draft' ] );

		update_post_meta( $term, 'subject_of', [ (string) $older, (string) $newer, (string) $draft ] );

		$this->assertSame( [ $newer, $older ], Glossary::get_subject_of_ids( $term ) );
	}

	public function test_get_mentioned_in_ids_is_newest_first_limited_and_skips_subjects() {
		$term    = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$old     = self::factory()->post->create( [ 'post_date' => '2026-01-01 00:00:00' ] );
		$mid     = self::factory()->post->create( [ 'post_type' => 'events', 'post_date' => '2026-03-01 00:00:00' ] );
		$new     = self::factory()->post->create( [ 'post_type' => 'resources', 'post_date' => '2026-06-01 00:00:00' ] );
		$subject = self::factory()->post->create( [ 'post_date' => '2026-09-01 00:00:00' ] );

		update_post_meta( $term, 'mentioned_in', [ (string) $old, (string) $new, (string) $mid, (string) $subject ] );
		update_post_meta( $term, 'subject_of', [ (string) $subject ] );

		$this->assertSame( [ $new, $mid, $old ], Glossary::get_mentioned_in_ids( $term ) );
		$this->assertSame( [ $new, $mid ], Glossary::get_mentioned_in_ids( $term, 2 ) );
		$this->assertSame( [ $subject ], Glossary::get_subject_of_ids( $term, 5 ) );
	}

	public function test_get_post_terms_keeps_editor_order_skips_unpublished_and_dedupes() {
		$post  = self::factory()->post->create();
		$b     = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$a     = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$c     = self::factory()->post->create( [ 'post_type' => 'glossary' ] );
		$draft = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_status' => 'draft' ] );

		update_post_meta( $post, 'glossary_about', [ (string) $b, (string) $draft, (string) $a ] );
		update_post_meta( $post, 'glossary_mentions', [ (string) $a, (string) $c ] );

		$this->assertSame( [ 'about' => [ $b, $a ], 'mentions' => [ $c ] ], Glossary::get_post_terms( $post ) );
	}

	/**
	 * @dataProvider overlap_cases
	 */
	public function test_a_pairing_cannot_be_both_subject_and_mention( $mention_key, $subject_key ) {
		$term_a = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Alpha' ] );
		$term_b = self::factory()->post->create( [ 'post_type' => 'glossary', 'post_title' => 'Beta' ] );
		$field  = acf_get_field( $mention_key );
		$glossary = new Glossary();

		$_POST['acf'] = [ $subject_key => [ (string) $term_a ] ];
		$result       = $glossary->validate_not_also_subject( true, [ (string) $term_a, (string) $term_b ], $field, 'acf[x]' );
		$clean        = $glossary->validate_not_also_subject( true, [ (string) $term_b ], $field, 'acf[x]' );
		$earlier      = $glossary->validate_not_also_subject( 'Earlier error', [ (string) $term_a ], $field, 'acf[x]' );
		unset( $_POST['acf'] );

		$this->assertSame( 'Also chosen as a subject: Alpha. Pick subject or mention, not both.', $result );
		$this->assertTrue( $clean );
		$this->assertSame( 'Earlier error', $earlier );
		$this->assertNotFalse( has_filter( 'acf/validate_value/key=' . $mention_key ), 'Validation is hooked.' );
	}

	public function overlap_cases() {
		return [
			'post sidebar' => [ Glossary::POST_MENTIONS_FIELD, Glossary::POST_ABOUT_FIELD ],
			'term screen'  => [ Glossary::MENTIONED_IN_FIELD, Glossary::SUBJECT_OF_FIELD ],
		];
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
				[ 'field_ltm_glossary_subject_of', 'subject_of', 'relationship' ],
				[ 'field_ltm_glossary_mentioned_in', 'mentioned_in', 'relationship' ],
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
