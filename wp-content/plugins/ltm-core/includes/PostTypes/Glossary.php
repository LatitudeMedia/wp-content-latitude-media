<?php
namespace LTMCore\PostTypes;

/**
 * Custom post type for Glossary terms.
 *
 * Each term is its own post. Editing uses the classic screen (no Gutenberg):
 * the title is the term, the excerpt is the short summary, and the full
 * definition, synonyms and related terms are ACF fields. Terms are listed on
 * a regular page via the latitudemedia/glossary-index block, and marked up
 * for search engines by \LTMCore\Seo\GlossarySchema.
 *
 * @package LTMCore
 */
class Glossary {

	/**
	 * Name of the custom post type.
	 *
	 * @var string
	 */
	public $name = 'glossary';

	/**
	 * Key of the related terms relationship field.
	 *
	 * @var string
	 */
	const RELATED_TERMS_FIELD = 'field_ltm_glossary_related_terms';

	/**
	 * Post-side "Subject of this content" field (editor sidebar): terms the
	 * post is about. Kept in sync with SUBJECT_OF_FIELD by ACF's
	 * bidirectional setting. Output as schema.org `about`.
	 *
	 * @var string
	 */
	const POST_ABOUT_FIELD = 'field_ltm_glossary_post_about';

	/**
	 * Post-side "Terms mentioned" field (editor sidebar): terms the post
	 * mentions in passing. Kept in sync with MENTIONED_IN_FIELD. Output as
	 * schema.org `mentions`.
	 *
	 * @var string
	 */
	const POST_MENTIONS_FIELD = 'field_ltm_glossary_post_mentions';

	/**
	 * Term-side "Subject of" relationship: posts about the term. Output as
	 * the term's schema.org `subjectOf`.
	 *
	 * @var string
	 */
	const SUBJECT_OF_FIELD = 'field_ltm_glossary_subject_of';

	/**
	 * Term-side "Mentioned in" relationship: posts that mention the term.
	 *
	 * @var string
	 */
	const MENTIONED_IN_FIELD = 'field_ltm_glossary_mentioned_in';

	/**
	 * Each "mention" field mapped to its "subject" counterpart on the same
	 * screen. A pairing can be a subject or a mention, not both.
	 *
	 * @var array<string, string>
	 */
	const MENTION_FIELD_COUNTERPARTS = [
		self::POST_MENTIONS_FIELD => self::POST_ABOUT_FIELD,
		self::MENTIONED_IN_FIELD  => self::SUBJECT_OF_FIELD,
	];

	/**
	 * Post types that can be linked to glossary terms.
	 *
	 * @var string[]
	 */
	const LINKED_POST_TYPES = [ 'post', 'podcasts', 'industry-news', 'resources', 'events' ];

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_post_type' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_block_editor' ), 10, 2 );
		// Priority 9: ahead of ACF's own add_meta_boxes callback (10), so the
		// summary box is registered first in the shared after-title slot.
		add_action( 'add_meta_boxes', array( $this, 'move_excerpt_meta_box' ), 9 );
		add_filter( 'cloudflare_purge_by_url', array( $this, 'purge_glossary_urls' ), 10, 2 );

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
			return;
		}

		add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
		add_filter( 'acf/fields/relationship/query/key=' . self::RELATED_TERMS_FIELD, array( $this, 'exclude_current_term' ), 10, 3 );
		add_action( 'acf/validate_save_post', array( $this, 'validate_summary' ) );
		foreach ( array_keys( self::MENTION_FIELD_COUNTERPARTS ) as $mention_field ) {
			add_filter( 'acf/validate_value/key=' . $mention_field, array( $this, 'validate_not_also_subject' ), 10, 4 );
		}
	}

	/**
	 * Rejects a "mention" that's also picked as a subject on the same screen,
	 * e.g. a term chosen in both "Subject of this content" and "Terms
	 * mentioned". A term is either a piece's subject or a passing mention.
	 *
	 * @param bool|string $valid Validation result so far.
	 * @param mixed       $value Submitted IDs for the mention field.
	 * @param array       $field The mention field.
	 * @param string      $input The input name.
	 * @return bool|string
	 */
	public function validate_not_also_subject( $valid, $value, $field, $input ) {
		if ( true !== $valid ) {
			return $valid;
		}

		$counterpart = self::MENTION_FIELD_COUNTERPARTS[ $field['key'] ] ?? '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- ACF verifies its own nonce before validating.
		$subjects = array_map( 'intval', (array) ( $_POST['acf'][ $counterpart ] ?? [] ) );
		$overlap  = array_intersect( array_map( 'intval', (array) ( $value ?: [] ) ), $subjects );

		if ( ! $overlap ) {
			return $valid;
		}

		return sprintf(
			/* translators: %s: comma-separated titles. */
			__( 'Also chosen as a subject: %s. Pick subject or mention, not both.', 'ltm' ),
			implode( ', ', array_map( 'get_the_title', $overlap ) )
		);
	}

	/**
	 * Warns in wp-admin that the glossary fields need ACF Pro active.
	 */
	public function missing_acf_notice() {
		echo '<div class="notice notice-warning"><p>' .
			esc_html__( 'Latitude Media Core: the Glossary term fields require Advanced Custom Fields Pro to be active.', 'ltm' ) .
			'</p></div>';
	}

	/**
	 * Glossary terms are edited on the classic screen. The post type has no
	 * `editor` support, which already rules Gutenberg out; this keeps it off
	 * even if that support is added later.
	 *
	 * @param bool   $use_block_editor Whether the post type uses the block editor.
	 * @param string $post_type        Post type name.
	 * @return bool
	 */
	public function disable_block_editor( $use_block_editor, $post_type ) {
		return $this->name === $post_type ? false : $use_block_editor;
	}

	/**
	 * Replaces the core Excerpt box with a required "Short summary" box (see
	 * render_summary_meta_box()) in ACF's after-title slot, so it sits above
	 * the Definition field.
	 *
	 * ACF registers its `acf_after_title` boxes at 'high' priority on
	 * add_meta_boxes (10); boxes of equal priority render in registration
	 * order, so registering this one first puts it on top.
	 *
	 * @param string $post_type Post type of the screen.
	 */
	public function move_excerpt_meta_box( $post_type ) {
		if ( $this->name !== $post_type ) {
			return;
		}

		remove_meta_box( 'postexcerpt', $this->name, 'normal' );
		add_meta_box(
			'postexcerpt',
			// One wrapping span: the postbox header is a flex row, which would
			// otherwise push the asterisk to the far side.
			'<span>' . esc_html__( 'Short summary', 'ltm' ) . ' <span class="acf-required">*</span></span>',
			array( $this, 'render_summary_meta_box' ),
			$this->name,
			'acf_after_title',
			'high'
		);
	}

	/**
	 * The excerpt field, with an explanation in place of core's "Excerpts are
	 * optional…" help text. Same `excerpt` input as core's box, so saving is
	 * unchanged.
	 *
	 * @param \WP_Post $post Post being edited.
	 */
	public function render_summary_meta_box( $post ) {
		?>
		<label class="screen-reader-text" for="excerpt"><?php esc_html_e( 'Short summary', 'ltm' ); ?></label>
		<textarea rows="1" cols="40" name="excerpt" id="excerpt"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
		<p class="description">
			<?php esc_html_e( 'Required. This is the post excerpt. It\'s shown under the term in the glossary list, and used as the search engine description.', 'ltm' ); ?>
		</p>
		<?php
	}

	/**
	 * Makes the Short summary required, through ACF's pre-submit validation:
	 * Publish/Update is blocked with a notice like any required ACF field,
	 * while Save Draft (which ACF doesn't validate) still works.
	 */
	public function validate_summary() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- ACF verifies its own nonce before this action.
		if ( $this->name !== acf_request_arg( 'post_type' ) ) {
			return;
		}

		if ( '' === trim( wp_unslash( $_POST['excerpt'] ?? '' ) ) ) {
			// No input name: the excerpt isn't an ACF field, so ACF can't flag it
			// inline; a global error puts the message in the top notice instead.
			acf_add_validation_error( '', __( 'Short summary is required.', 'ltm' ) );
		}
		// phpcs:enable
	}

	/**
	 * Adds the URLs a change affects to the Cloudflare purge.
	 *
	 * - A glossary term: the term itself, the glossary index page that lists
	 *   it, its related terms (which list it back), and the posts it's the
	 *   subject of or mentioned in (whose `about` / `mentions` metadata
	 *   describes it).
	 * - A linkable post (see LINKED_POST_TYPES): the terms it's about or
	 *   mentions, whose pages list it in their "Subject of" / "Mentioned in"
	 *   sidebar blocks (and `subjectOf` metadata). Cloudflare already purges
	 *   the post's own URL.
	 *
	 * @param string[] $urls    URLs Cloudflare will purge.
	 * @param int      $post_id Post that changed.
	 * @return string[]
	 */
	public function purge_glossary_urls( $urls, $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( $this->name === $post_type ) {
			$urls[] = get_permalink( $post_id );

			$index_page_id = \LTMCore\Blocks\GlossaryIndex::get_index_page_id();
			if ( $index_page_id ) {
				$urls[] = get_permalink( $index_page_id );
			}

			$affected = array_merge(
				self::get_stored_and_submitted_ids( $post_id, 'related_terms', self::RELATED_TERMS_FIELD ),
				self::get_stored_and_submitted_ids( $post_id, 'subject_of', self::SUBJECT_OF_FIELD ),
				self::get_stored_and_submitted_ids( $post_id, 'mentioned_in', self::MENTIONED_IN_FIELD )
			);
		} elseif ( in_array( $post_type, self::LINKED_POST_TYPES, true ) ) {
			$affected = array_merge(
				self::get_stored_and_submitted_ids( $post_id, 'glossary_about', self::POST_ABOUT_FIELD ),
				self::get_stored_and_submitted_ids( $post_id, 'glossary_mentions', self::POST_MENTIONS_FIELD )
			);
		} else {
			return $urls;
		}

		foreach ( array_unique( $affected ) as $affected_id ) {
			$urls[] = get_permalink( $affected_id );
		}

		return $urls;
	}

	/**
	 * Post IDs in a relationship-style field, both as stored and as just
	 * submitted. Cloudflare purges on transition_post_status, before ACF saves
	 * fields, so the stored value is still the old list (pages losing a link)
	 * and the submitted one is the new list (pages gaining one).
	 *
	 * @param int    $post_id   Post being saved.
	 * @param string $meta_name Field name (meta key).
	 * @param string $field_key Field key, as used in `$_POST['acf']`.
	 * @return int[]
	 */
	private static function get_stored_and_submitted_ids( $post_id, $meta_name, $field_key ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- read-only; WordPress verified the save.
		$submitted = $_POST['acf'][ $field_key ] ?? [];
		$ids       = array_merge( (array) get_post_meta( $post_id, $meta_name, true ), (array) $submitted );

		return array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
	}

	/**
	 * Keeps a term from being offered as related to itself.
	 *
	 * @param array      $args    WP_Query args for the relationship field's choices.
	 * @param array      $field   The field.
	 * @param int|string $post_id The post being edited.
	 * @return array
	 */
	public function exclude_current_term( $args, $field, $post_id ) {
		$args['post__not_in']   = (array) ( $args['post__not_in'] ?? array() );
		$args['post__not_in'][] = (int) $post_id;

		return $args;
	}

	/**
	 * A term's abbreviation (e.g. "BESS"), or '' when it has none.
	 *
	 * @param int $post_id Glossary term post ID.
	 * @return string
	 */
	public static function get_abbreviation( $post_id ) {
		return trim( (string) ( function_exists( 'get_field' ) ? get_field( 'abbreviation', $post_id ) : '' ) );
	}

	/**
	 * Synonym strings from a term's `synonyms` repeater.
	 *
	 * @param int $post_id Glossary term post ID.
	 * @return string[]
	 */
	public static function get_synonyms( $post_id ) {
		$rows = function_exists( 'get_field' ) ? get_field( 'synonyms', $post_id ) : [];

		return array_values( array_filter( array_map( 'trim', wp_list_pluck( $rows ?: [], 'synonym' ) ) ) );
	}

	/**
	 * IDs of a term's published related terms (`related_terms` relationship).
	 * Terms unpublished after being linked are left out.
	 *
	 * @param int $post_id Glossary term post ID.
	 * @return int[]
	 */
	public static function get_related_term_ids( $post_id ) {
		$ids = function_exists( 'get_field' ) ? get_field( 'related_terms', $post_id ) : [];

		return array_values(
			array_filter(
				array_map( 'intval', $ids ?: [] ),
				static fn( $id ) => 'publish' === get_post_status( $id )
			)
		);
	}

	/**
	 * The published glossary terms a post is about and mentions, each in the
	 * order the editor picked them. A term in both lists counts as a subject.
	 *
	 * @param int $post_id A post of one of LINKED_POST_TYPES.
	 * @return array{about: int[], mentions: int[]}
	 */
	public static function get_post_terms( $post_id ) {
		$has_acf  = function_exists( 'get_field' );
		$about    = self::published_ids( $has_acf ? get_field( 'glossary_about', $post_id, false ) : [] );
		$mentions = self::published_ids( $has_acf ? get_field( 'glossary_mentions', $post_id, false ) : [] );

		return [
			'about'    => $about,
			'mentions' => array_values( array_diff( $mentions, $about ) ),
		];
	}

	/**
	 * IDs of the published posts a term is the subject of, newest first.
	 *
	 * @param int $term_id Glossary term post ID.
	 * @param int $limit   Maximum number of posts, or -1 for all.
	 * @return int[]
	 */
	public static function get_subject_of_ids( $term_id, $limit = -1 ) {
		return self::get_term_post_ids( $term_id, 'subject_of', $limit );
	}

	/**
	 * IDs of the published posts that mention a term, newest first. Posts the
	 * term is also the subject of are left out.
	 *
	 * @param int $term_id Glossary term post ID.
	 * @param int $limit   Maximum number of posts, or -1 for all.
	 * @return int[]
	 */
	public static function get_mentioned_in_ids( $term_id, $limit = -1 ) {
		$ids = self::get_term_post_ids( $term_id, 'mentioned_in', -1 );
		$ids = array_values( array_diff( $ids, self::get_subject_of_ids( $term_id ) ) );

		return -1 === $limit ? $ids : array_slice( $ids, 0, $limit );
	}

	/**
	 * Published posts in one of a term's post relationships, newest first.
	 *
	 * @param int    $term_id    Glossary term post ID.
	 * @param string $field_name `subject_of` or `mentioned_in`.
	 * @param int    $limit      Maximum number of posts, or -1 for all.
	 * @return int[]
	 */
	private static function get_term_post_ids( $term_id, $field_name, $limit ) {
		$ids = self::published_ids( function_exists( 'get_field' ) ? get_field( $field_name, $term_id, false ) : [] );
		if ( ! $ids ) {
			return [];
		}

		return array_map(
			'intval',
			get_posts(
				[
					'post_type'      => self::LINKED_POST_TYPES,
					'post__in'       => $ids,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'posts_per_page' => $limit,
					'fields'         => 'ids',
					'no_found_rows'  => true,
				]
			)
		);
	}

	/**
	 * Keeps only the IDs of published posts.
	 *
	 * @param mixed $ids Raw field value (array of IDs, or empty).
	 * @return int[]
	 */
	private static function published_ids( $ids ) {
		return array_values(
			array_filter(
				array_map( 'intval', (array) ( $ids ?: [] ) ),
				static fn( $id ) => 'publish' === get_post_status( $id )
			)
		);
	}

	/**
	 * Registers the "Glossary term" field group, and the "Glossary terms"
	 * sidebar group (subject / mentions) on linkable posts.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_ltm_glossary_term',
			'title' => 'Glossary term',
			'fields' => array(
				array(
					'key' => 'field_ltm_glossary_abbreviation',
					'label' => 'Abbreviation',
					'name' => 'abbreviation',
					'type' => 'text',
					'instructions' => 'Optional. The short form of this term, e.g. BESS for Battery energy storage system. Shown in brackets after the term, used in the page\'s search-engine title, and matched by the glossary search.',
					'required' => 0,
					'maxlength' => 20,
				),
				array(
					'key' => 'field_ltm_glossary_definition',
					'label' => 'Definition',
					'name' => 'definition',
					'type' => 'wysiwyg',
					'required' => 1,
					'tabs' => 'all',
					'toolbar' => 'full',
					'media_upload' => 0,
				),
				array(
					'key' => 'field_ltm_glossary_synonyms',
					'label' => 'Synonyms',
					'name' => 'synonyms',
					'type' => 'repeater',
					'instructions' => 'Other names for this term. Matched by the glossary search.',
					'layout' => 'table',
					'button_label' => 'Add synonym',
					'sub_fields' => array(
						array(
							'key' => 'field_ltm_glossary_synonym',
							'label' => 'Synonym',
							'name' => 'synonym',
							'type' => 'text',
							'required' => 1,
							'parent_repeater' => 'field_ltm_glossary_synonyms',
						),
					),
				),
				array(
					'key' => self::RELATED_TERMS_FIELD,
					'label' => 'Related terms',
					'name' => 'related_terms',
					'type' => 'relationship',
					'instructions' => 'Linking a term here also lists this term on that term\'s page.',
					'post_type' => array( $this->name ),
					'post_status' => array( 'publish' ),
					'filters' => array( 'search' ),
					'return_format' => 'id',
					'bidirectional' => 1,
					'bidirectional_target' => array( self::RELATED_TERMS_FIELD ),
				),
				array(
					'key' => self::SUBJECT_OF_FIELD,
					'label' => 'Subject of',
					'name' => 'subject_of',
					'type' => 'relationship',
					'instructions' => 'Articles, podcasts, industry news, resources and events whose subject is this term. Search-engine metadata only, not shown on the page. Same links as "Subject of this content" in each one\'s editor sidebar.',
					'post_type' => self::LINKED_POST_TYPES,
					'post_status' => array( 'publish' ),
					'filters' => array( 'search', 'post_type' ),
					'return_format' => 'id',
					'bidirectional' => 1,
					'bidirectional_target' => array( self::POST_ABOUT_FIELD ),
				),
				array(
					'key' => self::MENTIONED_IN_FIELD,
					'label' => 'Mentioned in',
					'name' => 'mentioned_in',
					'type' => 'relationship',
					'instructions' => 'Content that mentions this term in passing. Search-engine metadata only. Same links as "Terms mentioned" in each one\'s editor sidebar. Each piece goes in one list or the other, not both.',
					'post_type' => self::LINKED_POST_TYPES,
					'post_status' => array( 'publish' ),
					'filters' => array( 'search', 'post_type' ),
					'return_format' => 'id',
					'bidirectional' => 1,
					'bidirectional_target' => array( self::POST_MENTIONS_FIELD ),
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => $this->name,
					),
				),
			),
			'position' => 'acf_after_title',
			'style' => 'default',
			'active' => true,
		));

		acf_add_local_field_group(array(
			'key' => 'group_ltm_glossary_post_terms',
			'title' => 'Glossary terms',
			'fields' => array(
				array(
					'key' => self::POST_ABOUT_FIELD,
					'label' => 'Subject of this content',
					'name' => 'glossary_about',
					'type' => 'post_object',
					'instructions' => 'Terms this piece is about. Tells search engines its subject (schema.org "about"). Not shown on the page.',
					'post_type' => array( $this->name ),
					'post_status' => array( 'publish' ),
					'multiple' => 1,
					'allow_null' => 1,
					'ui' => 1,
					'return_format' => 'id',
					'bidirectional' => 1,
					'bidirectional_target' => array( self::SUBJECT_OF_FIELD ),
				),
				array(
					'key' => self::POST_MENTIONS_FIELD,
					'label' => 'Terms mentioned',
					'name' => 'glossary_mentions',
					'type' => 'post_object',
					'instructions' => 'Terms mentioned in passing (schema.org "mentions"). Not shown on the page. A term goes in one list or the other, not both.',
					'post_type' => array( $this->name ),
					'post_status' => array( 'publish' ),
					'multiple' => 1,
					'allow_null' => 1,
					'ui' => 1,
					'return_format' => 'id',
					'bidirectional' => 1,
					'bidirectional_target' => array( self::MENTIONED_IN_FIELD ),
				),
			),
			'location' => array_map(
				static fn( $post_type ) => array(
					array(
						'param' => 'post_type',
						'operator' => '==',
						'value' => $post_type,
					),
				),
				self::LINKED_POST_TYPES
			),
			'position' => 'side',
			'style' => 'default',
			'active' => true,
		));
	}

	/**
	 * Creates the post type.
	 */
	public function create_post_type() {
		register_post_type(
			$this->name,
			[
				'labels' => [
					'name'                  => __( 'Glossary', 'ltm' ),
					'singular_name'         => __( 'Glossary term', 'ltm' ),
					'add_new'               => __( 'Add New Term', 'ltm' ),
					'add_new_item'          => __( 'Add New Glossary term', 'ltm' ),
					'edit_item'             => __( 'Edit Glossary term', 'ltm' ),
					'new_item'              => __( 'New Glossary term', 'ltm' ),
					'view_item'             => __( 'View Glossary term', 'ltm' ),
					'view_items'            => __( 'View Glossary terms', 'ltm' ),
					'search_items'          => __( 'Search Glossary terms', 'ltm' ),
					'not_found'             => __( 'No Glossary terms found', 'ltm' ),
					'not_found_in_trash'    => __( 'No Glossary terms found in Trash', 'ltm' ),
					'all_items'             => __( 'All Terms', 'ltm' ),
					'insert_into_item'      => __( 'Insert into Glossary term', 'ltm' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Glossary term', 'ltm' ),
					'filter_items_list'     => __( 'Filter Glossary terms list', 'ltm' ),
					'items_list_navigation' => __( 'Glossary terms list navigation', 'ltm' ),
					'items_list'            => __( 'Glossary terms list', 'ltm' ),
					'menu_name'             => __( 'Glossary', 'ltm' ),
				],
				'menu_icon'    => 'dashicons-book-alt',
				'public'       => true,
				'map_meta_cap' => true,
				'has_archive'  => false,
				'show_ui'      => true,
				'show_in_rest' => true,
				'rewrite'      => [ 'slug' => 'glossary', 'with_front' => false ],
				'supports'     => [ 'title', 'excerpt', 'revisions' ],
			]
		);
	}
}
