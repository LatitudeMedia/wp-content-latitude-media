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
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'create_post_type' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_block_editor' ), 10, 2 );
		// Priority 9: ahead of ACF's own add_meta_boxes callback (10), so the
		// summary box is registered first in the shared after-title slot.
		add_action( 'add_meta_boxes', array( $this, 'move_excerpt_meta_box' ), 9 );

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_acf_notice' ) );
			return;
		}

		add_action( 'acf/include_fields', array( $this, 'register_field_group' ) );
		add_filter( 'acf/fields/relationship/query/key=' . self::RELATED_TERMS_FIELD, array( $this, 'exclude_current_term' ), 10, 3 );
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
	 * Retitles the core Excerpt box "Short summary" (it's shown on the glossary
	 * index and used as the schema description) and moves it into ACF's
	 * after-title slot, so it sits above the Definition field.
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
		add_meta_box( 'postexcerpt', __( 'Short summary', 'ltm' ), 'post_excerpt_meta_box', $this->name, 'acf_after_title', 'high' );
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
	 * Registers the "Glossary term" field group.
	 */
	public function register_field_group() {
		acf_add_local_field_group(array(
			'key' => 'group_ltm_glossary_term',
			'title' => 'Glossary term',
			'fields' => array(
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
					'instructions' => 'Other names or abbreviations for this term.',
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
