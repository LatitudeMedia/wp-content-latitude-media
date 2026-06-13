<?php
/**
 * Ajax Handlers class
 */

namespace Jet_Elementor_Extension;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Ajax_Handlers.
 *
 * @since 1.0.0
 */
class Ajax_Handlers {

	/**
	 * Ajax_Handlers constructor.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		if ( wp_doing_ajax() ) {
			add_action( 'wp_ajax_jet_query_control_options', array( $this, 'get_query_control_options' ) );
			add_action( 'wp_ajax_jet_query_get_edit_url',    array( $this, 'get_edit_url' ) );
		}
	}

	public function get_edit_url() {

		if ( empty( $_REQUEST['id'] ) ) {
			wp_send_json_error();
		}

		$id         = $_REQUEST['id'];
		$edit_url   = '';
		$query_type = ! empty( $_REQUEST['query_type'] ) ? $_REQUEST['query_type'] : 'post';

		switch ( $query_type ) {
			case 'post':
			case 'elementor_templates':

				$is_build_with_elementor = ! ! get_post_meta( $id, '_elementor_edit_mode', true );

				if ( $is_build_with_elementor ) {
					$edit_url = \Elementor\Plugin::instance()->documents->get( $id )->get_edit_url();
				} else {
					$edit_url = get_edit_post_link( $id, '' );
				}

				break;

			case 'tax':
				$edit_url = get_edit_term_link( $id );
				break;
		}

		$data = array(
			'edit_url' => $edit_url,
		);

		wp_send_json_success( $data );
	}

	/**
	 * Generate a custom secret key for the cases when NONCE_KEY is not set.
	 *
	 * @return string
	 */
	public static function custom_sercret_key() {

		$secret_key = get_option( 'jet-elementor-extension-secret-key', '' );

		if ( ! $secret_key ) {
			$secret_key = wp_generate_password( 64, false );
			update_option( 'jet-elementor-extension-secret-key', $secret_key );
		}

		return $secret_key;
	}

	/**
	 * Create a signature for the request.
	 *
	 * @return void
	 */
	public static function create_signature( $query = array() ) {

		if ( ! is_array( $query ) ) {
			$query = array();
		}

		$allowed_keys = array(
			'post__not_in',
			'meta_query',
			'post_type',
			'posts_per_page',
		);

		foreach ( $allowed_keys as $key ) {
			if ( isset( $query[ $key ] ) ) {
				unset( $query[ $key ] );
			}
		}

		foreach ( $query as $key => $value ) {
			if ( ! is_array( $value ) ) {
				$query[ $key ] = (string) $value;
			}
		}

		ksort( $query );

		$secret_key = defined( 'NONCE_KEY' ) ? NONCE_KEY : self::custom_sercret_key();
		$signature = md5( wp_json_encode( $query ) . $secret_key );

		return $signature;
	}

	/**
	 * Sanitize query arguments.
	 *
	 * @param array $query_args Query arguments.
	 *
	 * @return array
	 */
	public function sanitize_query( $query_args ) {

		if ( ! is_array( $query_args ) ) {
			return array();
		}

		$allowed_keys = array(
			'post_type',
			'post__in',
			'tax_query',
			'meta_query',
			's_title', // Custom key for searching by title.
		);

		foreach ( $query_args as $key => $value ) {
			if ( ! in_array( $key, $allowed_keys, true ) ) {
				unset( $query_args[ $key ] );
			}
		}

		return $query_args;
	}

	/**
	 * Validate signature from request to ensure it wasn't hijacked
	 *
	 * @param array $data
	 * @return bool
	 */
	public function validate_request_signature( $data ) {

		$input_signature = ! empty( $data['signature'] ) ? $data['signature'] : false;
		$query = ! empty( $data['query'] ) ? $data['query'] : array();
		$generated_signature = self::create_signature( $query );

		return $input_signature === $generated_signature;
	}

	/**
	 * Get Query control options list.
	 */
	public function get_query_control_options() {

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error();
		}

		$data = $_REQUEST;

		if ( ! isset( $data['query_type'] ) ) {
			wp_send_json_error();
			return;
		}

		if ( ! $this->validate_request_signature( $data ) ) {
			wp_send_json_error();
		}

		$results = array();

		switch ( $data['query_type'] ) {
			case 'post':

				$default_query_args = array(
					'post_type'           => 'any',
					'post_status'         => 'publish',
					'posts_per_page'      => - 1,
					'suppress_filters'    => false,
					'ignore_sticky_posts' => true,
					'orderby'             => 'title',
					'order'               => 'ASC',
				);

				$query_args = ! empty( $data['query'] ) ? $this->sanitize_query( $data['query'] ) : array();
				$query_args = wp_parse_args( $query_args, $default_query_args );

				if ( ! empty( $data['q'] ) ) {
					$query_args['s_title'] = $data['q'];
				}

				if ( ! empty( $data['ids'] ) ) {
					$query_args['post__in'] = $data['ids'];
				}

				add_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

				$posts = get_posts( $query_args );

				remove_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10 );

				foreach ( $posts as $post ) {
					$results[] = array(
						'id'   => $post->ID,
						'text' => $post->post_title,
					);
				}

				break;

			case 'elementor_templates':
				$document_types = \Elementor\Plugin::instance()->documents->get_document_types( array(
					'show_in_library' => true,
				) );

				$default_query_args = array(
					'post_type'        => \Elementor\TemplateLibrary\Source_Local::CPT,
					'post_status'      => 'publish',
					'posts_per_page'   => - 1,
					'suppress_filters' => false,
					'orderby'          => 'title',
					'order'            => 'ASC',
					'meta_query'       => array(
						array(
							'key'     => \Elementor\Core\Base\Document::TYPE_META_KEY,
							'value'   => array_keys( $document_types ),
							'compare' => 'IN',
						),
					),
				);

				$query_args = ! empty( $data['query'] ) ? $data['query'] : array();
				$query_args = wp_parse_args( $query_args, $default_query_args );

				if ( ! empty( $data['q'] ) ) {
					$query_args['s_title'] = $data['q'];
				}

				if ( ! empty( $data['ids'] ) ) {
					$query_args['post__in'] = $data['ids'];
				}

				add_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10, 2 );

				$posts = get_posts( $query_args );

				remove_filter( 'posts_where', array( $this, 'force_search_by_title' ), 10 );

				foreach ( $posts as $post ) {
					$results[] = array(
						'id'   => $post->ID,
						'text' => sprintf( '%1$s (%2$s)', $post->post_title,  \Elementor\TemplateLibrary\Source_Local::get_template_type( $post->ID ) ),
					);
				}

				break;

			case 'tax':

				$default_terms_args = array(
					'hide_empty' => false,
				);

				$terms_args = ! empty( $data['query'] ) ? $data['query'] : array();
				$terms_args = wp_parse_args( $terms_args, $default_terms_args );

				if ( ! empty( $data['q'] ) ) {
					$terms_args['search'] = $data['q'];
				}

				if ( empty( $terms_args['taxonomy'] ) ) {
					$terms_args['taxonomy'] = get_taxonomies( array( 'show_in_nav_menus' => true ), 'names' );
				}

				if ( ! empty( $data['ids'] ) ) {
					$terms_args['include'] = $data['ids'];
				}

				$terms = get_terms( $terms_args );

				global $wp_taxonomies;

				foreach ( $terms as $term ) {
					$results[] = array(
						'id'   => $term->term_id,
						'text' => sprintf( '%1$s: %2$s', $wp_taxonomies[ $term->taxonomy ]->label, $term->name ),
					);
				}

				break;
		}

		$data = array(
			'results' => $results,
		);

		wp_send_json_success( $data );
	}

	/**
	 * Force query to look in post title while searching.
	 *
	 * @param  string $where
	 * @param  object $query
	 * @return string
	 */
	public function force_search_by_title( $where, $query ) {

		$args = $query->query;

		if ( ! isset( $args['s_title'] ) ) {
			return $where;
		}

		global $wpdb;

		$search = esc_sql( $wpdb->esc_like( $args['s_title'] ) );
		$where .= " AND {$wpdb->posts}.post_title LIKE '%$search%'";

		return $where;
	}
}
