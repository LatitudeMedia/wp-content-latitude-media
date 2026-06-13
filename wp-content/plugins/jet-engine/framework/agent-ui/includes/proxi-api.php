<?php
/**
 * Main class for the AI API Proxy REST endpoints.
 *
 * @package WordPress\Feature_API_Agent
 */

namespace Crocoblock\Agent_UI;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Registers and handles REST API endpoints for proxying AI service requests.
 */
class Proxi_API {

	public const NONCE_KEY = 'croco_agent_ui_nonce';

	/**
	 * Supported AI API service providers.
	 */
	private const SUPPORTED_AI_API_SERVICES = [ 'openai' ];

	/**
	 * Base URL for the OpenAI API.
	 */
	private const OPENAI_API_ROOT = 'https://api.openai.com/v1/';

	/**
	 * REST API namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'croco/v1';

	protected $current_path = '';

	/**
	 * REST API base route for the proxy.
	 *
	 * @var string
	 */
	protected $rest_base = 'ai-api-proxy';

	/**
	 * A reference to an instance of this class.
	 *
	 * @var Proxi_API
	 */
	private static $instance = null;

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return Proxi_API
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers WordPress hooks.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
	}

	/**
	 * Sets the current path for the module.
	 *
	 * @param string $path The path to set.
	 */
	public function set_current_path( $path ) {
		$this->current_path = rtrim( $path, '/' ) . '/';
	}

	/**
	 * Get Rest URL of the Proxi_API endpoint
	 *
	 * @param string $path
	 * @return string
	 */
	public function rest_url( $path = '' ) {
		return rest_url( $this->namespace . '/' . $this->rest_base . '/' . ltrim( $path, '/' ) );
	}

	/**
	 * Registers the REST API routes.
	 */
	public function register_rest_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/models',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'list_available_models' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/key',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'save_api_key' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'args'                => array(
					'api_key' => array(
						'description' => __( 'The API key to save.', 'wp-feature-api-agent' ),
						'type'        => 'string',
						'required'    => true,
					),
					'nonce' => array(
						'description' => __( 'Nonce for verifying the request.', 'wp-feature-api-agent' ),
						'type'        => 'string',
						'required'    => true,
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/model',
			array(
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => array( $this, 'save_model' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'args'                => array(
					'model' => array(
						'description' => __( 'The model to save.', 'wp-feature-api-agent' ),
						'type'        => 'string',
						'required'    => true,
					),
					'nonce' => array(
						'description' => __( 'Nonce for verifying the request.', 'wp-feature-api-agent' ),
						'type'        => 'string',
						'required'    => true,
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/clear-conversation',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'clear_conversation' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/response',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'get_response' ),
				'permission_callback' => array( $this, 'check_permissions' ),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<api_path>.*)',
			array(
				'methods'             => WP_REST_Server::ALLMETHODS,
				'callback'            => array( $this, 'ai_api_proxy' ),
				'permission_callback' => array( $this, 'check_permissions' ),
				'args'                => array(
					'api_path' => array(
						'description' => __( 'The path to proxy to the AI service API.', 'wp-feature-api-agent' ),
						'type'        => 'string',
						'required'    => true,
					),
				),
			)
		);
	}

	/**
	 * Checks if the current user has permissions to access protected endpoints.
	 *
	 * @return bool|WP_Error True if the user has permission, WP_Error otherwise.
	 */
	public function check_permissions() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you are not allowed to access this endpoint.', 'wp-feature-api-agent' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}

	/**
	 * Saves the API key.
	 *
	 * @param WP_REST_Request $request Incoming request data.
	 * @return WP_Error|WP_REST_Response Response object or error.
	 */
	public function save_api_key( WP_REST_Request $request ) {

		$parameters = $request->get_json_params();

		if ( ! isset( $parameters['api_key'] ) || empty( $parameters['api_key'] ) ) {
			return new WP_Error(
				'invalid_api_key',
				__( 'API key is required.', 'wp-feature-api-agent' ),
				[ 'status' => 400 ]
			);
		}

		// additional nonce check
		$nonce = isset( $parameters['nonce'] ) ? sanitize_text_field( $parameters['nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, Proxi_API::NONCE_KEY ) ) {
			return new WP_Error(
				'invalid_nonce',
				__( 'The page is expired. Please reload it and try again.', 'wp-feature-api-agent' ),
				[ 'status' => 403 ]
			);
		}

		$api_key = sanitize_text_field( $parameters['api_key'] );

		if ( 'reset' === $api_key ) {
			$api_key = '';
		}

		Storage::instance()->set_key( $api_key );

		return new WP_REST_Response(
			[ 'message' => __( 'API key saved successfully.', 'wp-feature-api-agent' ) ],
			200
		);
	}

	/**
	 * Lists all the models available from the configured providers (OpenAI).
	 *
	 * @param WP_REST_Request $request Incoming request data.
	 * @return WP_Error|WP_REST_Response Model list data or error.
	 */
	public function list_available_models( WP_REST_Request $request ) {

		$openai_models = $this->get_provider_model_list( 'openai' );

		$all_models = [];

		if ( is_array( $openai_models ) ) {
			foreach ( $openai_models as $model ) {
				if ( is_object( $model ) ) {
					$model->owned_by = 'openai';
					$all_models[]    = $model->id;
				}
			}
		}

		if ( empty( $all_models ) ) {
			return new WP_Error(
				'model_list_failed',
				__( 'Unable to retrieve model lists from any provider.', 'wp-feature-api-agent' ),
				[ 'status' => 500 ]
			);
		}

		$response_data = $all_models;

		return new WP_REST_Response( $response_data );
	}

	public function save_model( WP_REST_Request $request ) {

		$parameters = $request->get_json_params();

		if ( ! isset( $parameters['model'] ) || empty( $parameters['model'] ) ) {
			return new WP_Error(
				'invalid_model',
				__( 'Model is required.', 'wp-feature-api-agent' ),
				[ 'status' => 400 ]
			);
		}

		// additional nonce check
		$nonce = isset( $parameters['nonce'] ) ? sanitize_text_field( $parameters['nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, Proxi_API::NONCE_KEY ) ) {
			return new WP_Error(
				'invalid_nonce',
				__( 'The page is expired. Please reload it and try again.', 'wp-feature-api-agent' ),
				[ 'status' => 403 ]
			);
		}

		$model = sanitize_text_field( $parameters['model'] );
		Storage::instance()->set_model( $model );
		$resp_handler = $this->get_response_api_handler();
		$resp_handler->reset( get_current_user_id() );

		return new WP_REST_Response(
			[ 'message' => __( 'Model saved successfully.', 'wp-feature-api-agent' ) ],
			200
		);
	}

	public function clear_conversation( WP_REST_Request $request ) {

		$body = $request->get_body();

		if ( $body ) {
			$body = json_decode( $body, true );
		} else {
			$body = [];
		}

		if ( ! class_exists( '\Crocoblock\Agent_UI\Response_API_Handler' ) ) {
			require_once $this->current_path . 'includes/response-api-handler.php';
			require_once $this->current_path . 'includes/tool-dispatcher.php';
		}

		$resp_handler = $this->get_response_api_handler();

		$result = $resp_handler->reset( get_current_user_id() );

		return new WP_REST_Response(
			$result,
			200
		);
	}

	/**
	 * Handle response API request.
	 *
	 * @param WP_REST_Request $request Incoming request data.
	 * @return WP_Error|WP_REST_Response Vendor data or error.
	 */
	public function get_response( WP_REST_Request $request ) {

		$body = $request->get_body();

		$api_key = Storage::instance()->get_key();

		if ( ! $api_key ) {
			return new WP_Error(
				'no_api_key',
				'Please set your OpenAI API key in the settings.',
				[ 'status' => 403 ]
			);
		}

		$query_params = $request->get_query_params();

		if ( ! empty( $query_params ) ) {
			unset( $query_params['_envelope'] );
			unset( $query_params['_locale'] );
		}

		if ( $body ) {
			$body = json_decode( $body, true );
		} else {
			$body = [];
		}

		$resp_handler = $this->get_response_api_handler( [
			'api_key'   => $api_key,
			'model'     => Storage::instance()->get_model(),
			'api_base'  => self::OPENAI_API_ROOT,
			'tools'     => $body['tools'] ?? [],
			'tool_input' => $body['tool_input'] ?? null,
			'tool_choice' => $body['tool_choice'] ?? 'auto',
			'url_query' => $query_params
		] );

		$result = $resp_handler->send( get_current_user_id(), $body['message'] ?? '' );

		return new WP_REST_Response(
			$result,
			200
		);
	}

	/**
	 * Get instance of Response_API_Handler
	 *
	 * @param array $data
	 * @return Response_API_Handler
	 */
	public function get_response_api_handler( $data = [] ) {

		if ( ! class_exists( '\Crocoblock\Agent_UI\Response_API_Handler' ) ) {
			require_once $this->current_path . 'includes/response-api-handler.php';
			require_once $this->current_path . 'includes/tool-dispatcher.php';
		}

		return new Response_API_Handler( $data );
	}

	/**
	 * Proxies the request to the appropriate AI service (OpenAI).
	 *
	 * @param WP_REST_Request $request Incoming request data.
	 * @return WP_Error|WP_REST_Response Vendor data or error.
	 */
	public function ai_api_proxy( WP_REST_Request $request ) {
		$api_path = $request->get_param( 'api_path' );
		$method   = $request->get_method();
		$body     = $request->get_body();
		$headers  = $request->get_headers();

		$api_key = Storage::instance()->get_key();

		if ( ! $api_key ) {
			return new WP_Error(
				'no_api_key',
				'Please set your OpenAI API key in the settings.',
				[ 'status' => 403 ]
			);
		}

		// Set OpenAI as the target service
		$target_service = 'openai';
		$target_url     = self::OPENAI_API_ROOT . $api_path;
		$auth_header    = sprintf( 'Bearer %s', $api_key );

		$outgoing_headers = array(
			'Content-Type' => $headers['content_type'][0] ?? ( ! empty( $body ) ? 'application/json' : null ),
			'User-Agent'   => $this->get_user_agent(),
			'Authorization' => $auth_header,
		);

		$outgoing_headers = array_filter( $outgoing_headers );

		$query_params = $request->get_query_params();
		if ( ! empty( $query_params ) ) {
			unset( $query_params['_envelope'] );
			unset( $query_params['_locale'] );
			$target_url = add_query_arg( $query_params, $target_url );
		}

		$response = wp_remote_request(
			$target_url,
			array(
				'method'  => $method,
				'headers' => $outgoing_headers,
				'body'    => $body,
				'timeout' => 60,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'proxy_request_failed',
				__( 'Failed to connect to the AI service.', 'wp-feature-api-agent' ),
				array( 'status' => 502 )
			);
		}

		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_headers = wp_remote_retrieve_headers( $response );
		$response_body    = wp_remote_retrieve_body( $response );

		$client_headers = [];
		if ( isset( $response_headers['content-type'] ) ) {
			$client_headers['Content-Type'] = $response_headers['content-type'];
		}

		if ( isset( $response_headers['x-request-id'] ) ) {
			$client_headers['X-Request-ID'] = $response_headers['x-request-id'];
		}

		$wp_response = new WP_REST_Response( $response_body, $response_code );

		foreach ( $client_headers as $key => $value ) {
			$wp_response->header( $key, $value );
		}

		// Process JSON responses
		if ( isset( $client_headers['Content-Type'] ) && str_contains( strtolower( $client_headers['Content-Type'] ), 'application/json' ) ) {
			$decoded_body = json_decode( $response_body );
			if ( json_last_error() === JSON_ERROR_NONE ) {
				$wp_response->set_data( $decoded_body );
			}
		} else {
			$wp_response->set_data( $response_body );
		}

		return $wp_response;
	}

	/**
	 * Get user agent for AI API requests
	 *
	 * @return string
	 */
	public function get_user_agent() {
		return 'WordPress AI API Proxy/Crocoblock_Agent';
	}

	/**
	 * Returns the list of available models for a specific provider.
	 * Uses caching.
	 *
	 * @param string $provider The provider key ('openai').
	 * @return array List of models (structure depends on provider) or empty array on error/cache miss failure.
	 */
	private function get_provider_model_list( string $provider ): array {
		if ( ! in_array( $provider, self::SUPPORTED_AI_API_SERVICES, true ) ) {
			return [];
		}

		$api_key = Storage::instance()->get_key();

		if ( ! $api_key ) {
			return [];
		}

		$cached_models = Storage::instance()->get_models();

		if ( ! empty( $cached_models ) ) {
			return $cached_models;
		}

		$headers  = [];
		$api_path = '';

		switch ( $provider ) {
			case 'openai':
				$headers = [
					'Authorization' => sprintf( 'Bearer %s', $api_key ),
					'User-Agent'    => $this->get_user_agent(),
				];
				$api_path = self::OPENAI_API_ROOT . 'models';
				break;
		}

		if ( empty( $api_path ) ) {
			return [];
		}

		$response = wp_remote_get(
			$api_path,
			array(
				'headers' => $headers,
				'timeout' => 30,
			)
		);

		if ( is_wp_error( $response ) ) {
			return [];
		}

		$body = wp_remote_retrieve_body( $response );
		if ( ! $body ) {
			return [];
		}

		$json_data = json_decode( $body );
		if ( ! $json_data || ! is_object( $json_data ) ) {
			return [];
		}

		$models_data = [];
		if ( $provider === 'openai' && isset( $json_data->data ) && is_array( $json_data->data ) ) {
			$models_data = $json_data->data;
		} else {
			return [];
		}

		if ( is_array( $models_data ) ) {
			Storage::instance()->set_models( $models_data );
			return $models_data;
		} else {
			return [];
		}
	}
}
