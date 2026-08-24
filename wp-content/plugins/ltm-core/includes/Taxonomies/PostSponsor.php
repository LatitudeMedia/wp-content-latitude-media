<?php
namespace LTMCore\Taxonomies;

/**
 * Taxonomy backing the Post <-> Sponsor relationship
 *
 * @package LTMCore
 */

/**
 * Class for the sponsor taxonomy.
 *
 * Terms are kept in sync with Sponsors post type entries (see
 * LTMCore\PostTypes\Sponsors), and are assigned to posts via a
 * single-select "Sponsors" meta box instead of the default taxonomy UI.
 */
class PostSponsor {

	/**
	 * Name of the taxonomy.
	 *
	 * @var string
	 */
	public $name = 'sponsor';

	/**
	 * Object types for this taxonomy.
	 *
	 * @var array
	 */
	public $object_types;

	/**
	 * Build the taxonomy object.
	 */
	public function __construct() {
		$this->object_types = [ 'post', 'thematic-pages' ];

		add_action( 'init', array( $this, 'create_taxonomy' ), 0 );
		add_action( 'rest_api_init', array( $this, 'register_rest_field' ) );

		foreach ( $this->object_types as $post_type ) {
			add_action( "add_meta_boxes_{$post_type}", array( $this, 'add_meta_box' ) );
			add_action( "save_post_{$post_type}", array( $this, 'save_meta_box' ), 10, 2 );
		}
	}

	/**
	 * Creates the taxonomy.
	 */
	public function create_taxonomy() {
		register_taxonomy(
			$this->name,
			$this->object_types,
			[
				'labels' => [
					'name'          => __( 'Sponsors', 'ltm' ),
					'singular_name' => __( 'Sponsor', 'ltm' ),
				],
				'public'             => false,
				'show_ui'            => false,
				'show_in_rest'       => false,
				'show_admin_column'  => false,
				'hierarchical'       => false,
				'capabilities'       => [
					'manage_terms' => 'do_not_allow',
					'edit_terms'   => 'do_not_allow',
					'delete_terms' => 'do_not_allow',
					'assign_terms' => 'edit_posts',
				],
			]
		);
	}

	/**
	 * Registers the "Sponsors" meta box on the post editor.
	 */
	public function add_meta_box( $post ) {
		add_meta_box(
			'ltm_sponsor',
			__( 'Is Sponsored By', 'ltm' ),
			array( $this, 'render_meta_box' ),
			$post->post_type,
			'side',
			'high'
		);
	}

	/**
	 * Renders the sponsor dropdown.
	 */
	public function render_meta_box( $post ) {
		wp_nonce_field( 'ltm_sponsor_save', 'ltm_sponsor_nonce' );

		$current  = wp_get_object_terms( $post->ID, $this->name, [ 'fields' => 'ids' ] );
		$selected = ! empty( $current ) ? (int) $current[0] : 0;

		echo '<select name="ltm_sponsor" id="ltm_sponsor" class="postform" style="width:100%;max-width:100%;box-sizing:border-box;">';
		printf(
			'<option value="0"%s>%s</option>',
			selected( $selected, 0, false ),
			esc_html__( 'Not Sponsored', 'ltm' )
		);

		foreach ( $this->get_sponsor_term_map() as $sponsor_post_id => $term ) {
			printf(
				'<option value="%d"%s>%s</option>',
				$term->term_id,
				selected( $selected, $term->term_id, false ),
				esc_html( get_the_title( $sponsor_post_id ) )
			);
		}

		echo '</select>';
	}

	/**
	 * Saves the selected sponsor as the post's single sponsor term.
	 */
	public function save_meta_box( $post_id, $post ) {
		if ( ! isset( $_POST['ltm_sponsor_nonce'] ) || ! wp_verify_nonce( $_POST['ltm_sponsor_nonce'], 'ltm_sponsor_save' ) ) {
			return;
		}

		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$term_id = isset( $_POST['ltm_sponsor'] ) ? absint( $_POST['ltm_sponsor'] ) : 0;

		wp_set_object_terms( $post_id, $term_id ? [ $term_id ] : [], $this->name, false );
	}

	/**
	 * Builds a map of sponsor post ID => synced term object.
	 *
	 * @return array<int, \WP_Term>
	 */
	public function get_sponsor_term_map() {
		$terms = get_terms(
			[
				'taxonomy'   => $this->name,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]
		);

		$map = [];
		if ( is_wp_error( $terms ) ) {
			return $map;
		}

		foreach ( $terms as $term ) {
			$sponsor_post_id = get_term_meta( $term->term_id, '_sponsor_post_id', true );
			if ( $sponsor_post_id ) {
				$map[ (int) $sponsor_post_id ] = $term;
			}
		}

		return $map;
	}

	/**
	 * Exposes the currently selected sponsor as a read-only REST field, so
	 * the block editor (e.g. the Title Block's sponsor logo override) can
	 * react to sponsorship without a page reload.
	 */
	public function register_rest_field() {
		foreach ( $this->object_types as $post_type ) {
			register_rest_field(
				$post_type,
				'ltm_sponsor',
				[
					'get_callback' => array( $this, 'get_rest_sponsor' ),
					'schema'       => [
						'type'     => [ 'object', 'null' ],
						'context'  => [ 'edit' ],
						'readonly' => true,
					],
				]
			);
		}
	}

	/**
	 * REST callback: returns the linked sponsor's id, name, and logo URL.
	 */
	public function get_rest_sponsor( $object ) {
		$sponsor = get_post_sponsor( $object['id'] );

		if ( ! $sponsor ) {
			return null;
		}

		return [
			'id'   => $sponsor->ID,
			'name' => $sponsor->post_title,
			'logo' => get_the_post_thumbnail_url( $sponsor->ID, 'medium' ) ?: null,
		];
	}
}
