<?php
namespace LTMCore\PostTypes;

use LTMCore\Taxonomies\ThematicPageTypes;

/**
 * Custom post type for Thematic Pages
 *
 * @package LTMCore
 */

/**
 * Class for the Thematic Pages post type.
 */
class ThematicPages {

	/**
	 * Name of the custom post type.
	 *
	 * @var string
	 */
	public $name = 'thematic-pages';

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Create the post type
		add_action( 'init', array( $this, 'create_post_type' ) );

		// Keep the thematic-tag taxonomy in sync with thematic-pages posts
		add_action( 'save_post_' . $this->name, array( $this, 'sync_taxonomy_term' ), 10, 2 );
		add_action( 'before_delete_post', array( $this, 'delete_taxonomy_term' ) );

		// "Display in Thematic Page" only makes sense on regular posts; hide
		// it from the Thematic Page's own editor.
		add_action( 'enqueue_block_editor_assets', array( $this, 'hide_taxonomy_panel' ) );

		// The "Latitude Media Page Blocks" category is built for regular Pages;
		// keep it out of the Thematic Page inserter.
		add_filter( 'allowed_block_types_all', array( $this, 'restrict_page_blocks_category' ), 10, 2 );

		// Widen the editor canvas for Thematic Pages.
		add_action( 'enqueue_block_editor_assets', array( $this, 'editor_styles' ) );
	}

	/**
	 * Creates the post type.
	 *
	 * Note: the internal post_type key stays `thematic-pages` while the
	 * public URL slug is `themes` — see the `rewrite` arg below. This is
	 * deliberate; do not rename either independently.
	 */
	public function create_post_type() {
		register_post_type(
			$this->name,
			[
				'labels' => [
					'name'                  => __( 'Thematic Pages', 'ltm' ),
					'singular_name'         => __( 'Thematic Page', 'ltm' ),
					'add_new'               => __( 'Add New Thematic Page', 'ltm' ),
					'add_new_item'          => __( 'Add New Thematic Page', 'ltm' ),
					'edit_item'             => __( 'Edit Thematic Page', 'ltm' ),
					'new_item'              => __( 'New Thematic Page', 'ltm' ),
					'view_item'             => __( 'View Thematic Page', 'ltm' ),
					'view_items'            => __( 'View Thematic Pages', 'ltm' ),
					'search_items'          => __( 'Search Thematic Pages', 'ltm' ),
					'not_found'             => __( 'No Thematic Pages found', 'ltm' ),
					'not_found_in_trash'    => __( 'No Thematic Pages found in Trash', 'ltm' ),
					'parent_item_colon'     => __( 'Parent Thematic Page:', 'ltm' ),
					'all_items'             => __( 'All Thematic Pages', 'ltm' ),
					'archives'              => __( 'Thematic Page Archives', 'ltm' ),
					'attributes'            => __( 'Thematic Page Attributes', 'ltm' ),
					'insert_into_item'      => __( 'Insert into Thematic Page', 'ltm' ),
					'uploaded_to_this_item' => __( 'Uploaded to this Thematic Page', 'ltm' ),
					'filter_items_list'     => __( 'Filter Thematic Pages list', 'ltm' ),
					'items_list_navigation' => __( 'Thematic Pages list navigation', 'ltm' ),
					'items_list'            => __( 'Thematic Pages list', 'ltm' ),
					'menu_name'             => __( 'Thematic Pages', 'ltm' ),
				],
				'menu_icon'           => 'dashicons-editor-kitchensink',
				'public'              => true,
				'map_meta_cap'        => true,
				'has_archive'         => false,
				'show_ui'             => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'rewrite'             => [ 'slug' => 'themes' ],
				'supports'            => [ 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ],
				'template'            => [
					[ 'latitudemedia/title-block' ],
					[ 'latitudemedia/featured-post-block' ],
					[ 'core/heading', [ 'level' => 2 ] ],
					[ 'core/paragraph' ],
					[ 'latitudemedia/category-post-listing' ],
					[ 'core/heading', [ 'level' => 2 ] ],
					[ 'core/paragraph' ],
					[
						'core/columns',
						[
							'isStackedOnMobile' => true,
							'metadata'          => [ 'name' => __( 'Right Sidebar Layout', 'ltm' ) ],
						],
						[
							[
								'core/column',
								[
									'width'    => '66%',
									'metadata' => [ 'name' => __( 'Main Column', 'ltm' ) ],
								],
								[
									[ 'latitudemedia/category-post-listing' ],
								],
							],
							[
								'core/column',
								[
									'width'    => '33%',
									'metadata' => [ 'name' => __( 'Sidebar', 'ltm' ) ],
								],
								[
									[ 'latitudemedia/subscriber-form' ],
									[ 'core/html' ],
									[ 'latitudemedia/news-type-preview' ],
								],
							],
						],
					],
				],
			]
		);
	}

	/**
	 * Hides the "Display in Thematic Page" taxonomy panel when editing a
	 * Thematic Page itself — that taxonomy is for tagging regular posts with
	 * the thematic page they belong to, not for tagging a thematic page.
	 */
	public function hide_taxonomy_panel() {
		$screen = get_current_screen();

		if ( ! $screen || $screen->post_type !== $this->name ) {
			return;
		}

		$taxonomy = new ThematicPageTypes();

		wp_add_inline_script(
			'wp-edit-post',
			sprintf(
				"wp.domReady( function() { wp.data.dispatch( 'core/edit-post' ).removeEditorPanel( 'taxonomy-panel-%s' ); } );",
				esc_js( $taxonomy->name )
			)
		);
	}

	/**
	 * Removes every block in the "Latitude Media Page Blocks" category
	 * (slug `ltm-page-blocks`) from the inserter when editing a Thematic Page.
	 */
	public function restrict_page_blocks_category( $allowed_block_types, $editor_context ) {
		if ( empty( $editor_context->post ) || $editor_context->post->post_type !== $this->name ) {
			return $allowed_block_types;
		}

		if ( ! is_array( $allowed_block_types ) || empty( $allowed_block_types ) ) {
			$registered_blocks   = \WP_Block_Type_Registry::get_instance()->get_all_registered();
			$allowed_block_types = array_keys( $registered_blocks );
		}

		$excluded_blocks = array();

		foreach ( \WP_Block_Type_Registry::get_instance()->get_all_registered() as $block_name => $block_type ) {
			if ( isset( $block_type->category ) && 'ltm-page-blocks' === $block_type->category ) {
				$excluded_blocks[] = $block_name;
			}
		}

		return array_values( array_diff( $allowed_block_types, $excluded_blocks ) );
	}

	/**
	 * Widens the block editor canvas for Thematic Pages so blocks aren't
	 * constrained to the default content width.
	 */
	public function editor_styles() {
		$screen = get_current_screen();

		if ( ! $screen || $screen->post_type !== $this->name ) {
			return;
		}

		wp_add_inline_style(
			'wp-edit-blocks',
			'.post-type-thematic-pages.editor-styles-wrapper :where(.wp-block) { max-width: 95vw; }'
		);
	}

	/**
	 * Creates or updates the thematic-tag term derived from this post.
	 */
	public function sync_taxonomy_term( $post_id, $post ) {
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( in_array( $post->post_status, [ 'auto-draft', 'trash' ], true ) ) {
			return;
		}

		$taxonomy = new ThematicPageTypes();
		$term_id  = $this->get_synced_term_id( $post_id, $taxonomy->name );

		if ( $term_id ) {
			wp_update_term(
				$term_id,
				$taxonomy->name,
				[
					'name' => $post->post_title,
					'slug' => $post->post_name,
				]
			);
			return;
		}

		$result = wp_insert_term(
			$post->post_title,
			$taxonomy->name,
			[ 'slug' => $post->post_name ]
		);

		if ( ! is_wp_error( $result ) ) {
			add_term_meta( $result['term_id'], '_thematic_page_id', $post_id, true );
		}
	}

	/**
	 * Removes the synced thematic-tag term when the source page is permanently deleted.
	 */
	public function delete_taxonomy_term( $post_id ) {
		if ( get_post_type( $post_id ) !== $this->name ) {
			return;
		}

		$taxonomy = new ThematicPageTypes();
		$term_id  = $this->get_synced_term_id( $post_id, $taxonomy->name );

		if ( $term_id ) {
			wp_delete_term( $term_id, $taxonomy->name );
		}
	}

	/**
	 * Finds the thematic-tag term linked to a thematic page via term meta.
	 *
	 * Public so other classes (e.g. Blocks\CategoryPostListing) can resolve
	 * a thematic page's synced term without duplicating this lookup.
	 */
	public function get_synced_term_id( $post_id, $taxonomy_name ) {
		$terms = get_terms(
			[
				'taxonomy'   => $taxonomy_name,
				'hide_empty' => false,
				'meta_key'   => '_thematic_page_id',
				'meta_value' => $post_id,
				'fields'     => 'ids',
			]
		);

		return ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0] : 0;
	}
}
