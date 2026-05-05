<?php

namespace Jet_Engine\Modules\Profile_Builder\Bricks_Views;

use Jet_Engine\Modules\Profile_Builder\Module;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {
	/**
	 * Elementor Frontend instance
	 *
	 * @var null
	 */
	public $frontend = null;

	/**
	 * Constructor for the class
	 */
	function __construct() {
		if ( ! $this->has_bricks() ) {
			return;
		}

		add_action( 'jet-engine/bricks-views/init', array( $this, 'init' ), 10 );
		add_filter( 'jet-engine/listing/grid/lazy-load/post-id', array( $this, 'resolve_lazy_load_post_id' ) );
	}

	public function init() {
		add_action( 'jet-engine/bricks-views/register-elements', array( $this, 'register_elements' ), 11 );
		add_filter( 'jet-engine/profile-builder/template/content', array( $this, 'render_template_content' ), 0, 4 );
		add_filter( 'jet-engine/profile-builder/settings/template-sources', array( $this, 'register_templates_source' ) );
		add_filter( 'jet-engine/profile-builder/create-template/bricks_template', array( $this, 'create_profile_template' ), 10, 3 );
	}

	public function register_elements() {
		$element_files = array(
			$this->module_path( 'profile-content.php' ),
			$this->module_path( 'profile-menu.php' ),
		);

		foreach ( $element_files as $file ) {
			\Bricks\Elements::register_element( $file );
		}
	}

	public function module_path( $relative_path = '' ) {
		return jet_engine()->plugin_path( 'includes/modules/profile-builder/inc/bricks-views/elements/' . $relative_path );
	}

	/**
	 * Check if profile template is Bricks template, render it with Bricks
	 *
	 * @param  string $content     Initial content
	 * @param  int    $template_id template ID to render
	 * @return string
	 */
	public function render_template_content( $content, $template_id, $frontend, $template ) {
		if ( BRICKS_DB_TEMPLATE_SLUG !== $template->post_type ) {
			return $content;
		}

		return \Bricks\Theme::instance()->templates->render_shortcode( [ 'id' => $template_id ] );
	}

	/**
	 * Add Bricks templates to allowed profile builder templates
	 *
	 * @param  array $sources Initial sources list
	 * @return array
	 */
	public function register_templates_source( $sources ) {
		$sources['bricks_template'] = __( 'Bricks Template', 'jet-engine' );
		return $sources;
	}

	public function create_profile_template( $result = [], $template_name = '', $template_view = '' ) {
		if ( ! $template_name ) {
			return $result;
		}

		$template_id = wp_insert_post( [
			'post_title' => $template_name,
			'post_type'   => BRICKS_DB_TEMPLATE_SLUG,
			'post_status' => 'publish',
		] );

		if ( ! $template_id ) {
			return $result;
		}

		update_post_meta(
			$template_id,
			BRICKS_DB_TEMPLATE_TYPE,
			'section'
		);

		return [
			'template_url' => add_query_arg( [ 'bricks' => 'run' ], get_permalink( $template_id ) ),
			'template_id'  => $template_id,
		];
	}

	/**
	 * Detects the correct template/post ID for JetEngine Listing lazy load inside Bricks.
	 *
	 * Handle JetEngine Profile Builder pages (account/users/single user)
	 *
	 * @param int   $post_id  The current post or template ID.
	 *
	 * @return int Resolved post/template ID.
	 */
	public function resolve_lazy_load_post_id( $post_id ) {
		if ( ! bricks_is_frontend() ) {
			return $post_id;
		}

		$module              = Module::instance();
		$is_account_page     = $module->query->is_account_page();
		$is_users_page       = $module->query->is_users_page();
		$is_single_user_page = $module->query->is_single_user_page();

		if ( $is_account_page || $is_users_page || $is_single_user_page ) {
			$post_id = $module->frontend->get_template_id();
		}

		return $post_id;
	}

	public function has_bricks() {
		return ( defined( 'BRICKS_VERSION' ) && \Jet_Engine\Modules\Performance\Module::instance()->is_tweak_active( 'enable_bricks_views' ) );
	}
}