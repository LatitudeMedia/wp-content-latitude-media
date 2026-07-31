<?php
namespace LatitudeMedia\Taxonomies;

/**
 * Taxonomy backing the Post <-> Sponsor relationship
 *
 * @package LatitudeMedia
 */

/**
 * Class for the sponsor taxonomy.
 *
 * Terms are kept in sync with Sponsors post type entries (see
 * LatitudeMedia\PostTypes\Sponsors), and are assigned to posts via a
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
		$this->object_types = [ 'post' ];

		add_action( 'init', array( $this, 'create_taxonomy' ) );

		add_action( 'add_meta_boxes_post', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_post', array( $this, 'save_meta_box' ), 10, 2 );
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
	public function add_meta_box() {
		add_meta_box(
			'ltm_sponsor',
			__( 'Is Sponsored By', 'ltm' ),
			array( $this, 'render_meta_box' ),
			'post',
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
}
