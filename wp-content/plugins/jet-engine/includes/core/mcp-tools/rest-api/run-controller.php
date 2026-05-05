<?php
namespace Jet_Engine\MCP_Tools\Rest_API;

use Jet_Engine\MCP_Tools\Registry;
use WP_REST_Controller;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Error;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class Run_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'jet-engine/v1';
		$this->rest_base = 'mcp-tools/run';
		$this->register_routes();
	}

	/**
	 * Generate a nonce for the run controller.
	 *
	 * @return string
	 */
	public static function get_nonce() {
		return wp_create_nonce( 'wp_rest' );
	}

	/**
	 * Verify a nonce for the run controller.
	 *
	 * @param string $nonce The nonce to verify.
	 * @return bool
	 */
	public static function verify_nonce( $nonce ) {
		return wp_verify_nonce( $nonce, 'wp_rest' );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace, '/' . $this->rest_base . '/(?P<name>[a-zA-Z0-9\-\/]+?)',
			[
				[
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => [ $this, 'run_item' ],
					'permission_callback' => [ $this, 'run_item_permissions_check' ],
					'args'                => $this->get_collection_params(),
				],
			]
		);
	}

	/**
	 * Check if a given request has access to run an item.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error
	 */
	public function run_item_permissions_check( $request ) {

		$feature_name = $request->get_param( 'name' );
		$nonce        = $request->get_header( 'X-WP-Nonce' );

		if ( ! $nonce || ! self::verify_nonce( $nonce ) ) {
			return new WP_Error(
				'invalid_nonce',
				esc_html__( 'Invalid nonce. Please reload page and try again', 'jet-engine' ),
				[ 'status' => 403 ]
			);
		}

		if ( ! $feature_name ) {
			return new WP_Error( 'no_feature_id', esc_html__( 'Feature ID is required.', 'jet-engine' ), [ 'status' => 400 ] );
		}

		$feature = Registry::instance()->get_feature( $feature_name );

		if ( ! $feature ) {
			return new WP_Error( 'invalid_feature_id', esc_html__( 'Invalid Feature ID.', 'jet-engine' ), [ 'status' => 400 ] );
		}

		return $feature->check_permissions( $request );
	}

	/**
	 * Run a feature.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function run_item( $request ) {

		$feature_name = $request->get_param( 'name' );
		$input        = $request->get_param( 'input' );

		if ( ! $feature_name ) {
			return new WP_Error( 'no_feature_id', esc_html__( 'Feature ID is required.', 'jet-engine' ), [ 'status' => 400 ] );
		}

		$feature = Registry::instance()->get_feature( $feature_name );

		if ( ! $feature ) {
			return new WP_Error( 'invalid_feature_id', esc_html__( 'Invalid Feature ID.', 'jet-engine' ), [ 'status' => 400 ] );
		}

		$result = $feature->run( $input );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new WP_REST_Response( $result, 200 );
	}

	/**
	 * Get the query params for the run item.
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return [
			'input' => [
				'description' => __( 'The input data for the feature.', 'jet-engine' ),
				'type'        => 'object',
				'required'    => false,
			],
		];
	}
}
