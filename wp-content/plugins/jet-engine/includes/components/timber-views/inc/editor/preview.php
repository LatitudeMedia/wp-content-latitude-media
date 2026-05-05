<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\Editor;

use Jet_Engine\Timber_Views\Package;
use Timber\Timber;
use Timber\Post;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Preview {

	private $nonce_action = 'jet_engine_timber_editor';

	public function __construct() {
		add_action( 'wp_ajax_' . $this->get_action(), [ $this, 'do_action' ] );
	}

	public function get_action() {
		return 'jet_engine_timber_reload_preview';
	}

	public function nonce() {
		return wp_create_nonce( $this->nonce_action );
	}

	public function verify_request() {

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], $this->nonce_action ) ) {
			wp_send_json_error( [
				'message' => __( 'Link is expired', 'jet-engine' ),
			] );
		}

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error( [
				'message' => __( 'Incomplete request', 'jet-engine' ),
			] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [
				'message' => __( 'You do not have permission to perform this action', 'jet-engine' ),
			] );
		}

		return true;
	}

	/**
	 * Sanitize settings array
	 *
	 * @param array $settings Settings to sanitize.
	 *
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $settings ) {

		if ( ! is_array( $settings ) ) {
			return [];
		}

		return \Jet_Engine_Tools::sanitize_array_recursively( $settings );
	}

	public function do_action() {

		$this->verify_request();

		$settings   = ! empty( $_POST['settings'] ) ? $this->sanitize_settings( $_POST['settings'] ) : [];
		$listing_id = ! empty( $_POST['id'] ) ? absint( $_POST['id'] ) : false;
		$preview    = new \Jet_Engine_Listings_Preview( $settings, $listing_id );

		$preview_object = $preview->get_preview_object();

		if ( $preview_object && 'WP_Post' === get_class( $preview_object ) ) {
			global $post;
			$post = $preview_object;
		}

		do_action( 'jet-engine/twig-views/editor/before-render-preview', $preview_object, $this );

		try {
			$preview_html = Package::instance()->render_html(
				// Content is sanitized inside the render_html() by Package::sanitize_twig_content().
				wp_unslash( $_POST['html'] ),
				Package::instance()->get_context_for_object( $preview_object )
			);
		} catch ( \Exception $e ) {
			wp_send_json_error( [
				'message' => __( 'Error rendering preview: ', 'jet-engine' ) . $e->getMessage(),
			] );
			return;
		}

		wp_send_json_success( [
			'preview' => $preview_html
		] );
	}
}
