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

class MCP_Controller extends WP_REST_Controller {

	/**
	 * Server name for MCP initialize response.
	 * @var string
	 */
	protected $server_name = 'Crocoblock Client MCP Server';

	/**
	 * Server version for MCP initialize response.
	 * @var string
	 */
	protected $server_version = '1.0.0';

	/**
	 * Supported MCP protocol version.
	 *
	 * @var string
	 */
	protected $protocol_version = '2025-03-26';

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->namespace = 'jet-engine/v1';
		$this->rest_base = 'mcp';
		$this->register_routes();
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		register_rest_route( $this->namespace, '/' . $this->rest_base, [
			[
				'methods'             => [
					WP_REST_Server::READABLE,
					WP_REST_Server::CREATABLE
				],
				'callback'            => [ $this, 'handle_request' ],
				'permission_callback' => [ $this, 'permissions_check' ],
				'args'                => [],
			],
		] );
	}

	/**
	 * Check if a given request has access to get items.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error
	 */
	public function permissions_check( $request ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_forbidden',
				esc_html__( 'You cannot access this resource.', 'jet-engine' ),
				[ 'status' => rest_authorization_required_code() ]
			);
		}

		return true;
	}

	/**
	 * Build a JSON-RPC error response.
	 *
	 * @param mixed  $id
	 * @param int    $code
	 * @param string $message
	 * @return array
	 */
	protected function rpc_error( $id, int $code, string $message ): array {
		return [
			'jsonrpc' => '2.0',
			'id'      => $id,
			'error'   => [
				'code'    => $code,
				'message' => $message,
			],
		];
	}

	/**
	 * Build a JSON-RPC success response.
	 *
	 * @param mixed $id
	 * @param mixed $result
	 * @return array
	 */
	protected function rpc_response( $id, $result ): array {
		return [
			'jsonrpc' => '2.0',
			'id'      => $id,
			'result'  => $result,
		];
	}

	/**
	 * Main handler: JSON-RPC over HTTP.
	 *
	 * @param WP_REST_Request $request
	 * @return WP_REST_Response
	 */
	public function handle_request( $request ) {

		$method = $request->get_method();

		// MCP Streamable HTTP may use GET for SSE; if you don't stream, reply 405.
		if ( 'GET' === $method ) {
			return new WP_REST_Response( [ 'error' => 'GET not supported for this endpoint' ], 405 );
		}

		$raw = (string) $request->get_body();
		$dec = json_decode( $raw, true );

		// Accept either a single object or a batch array.
		if ( ! is_array( $dec ) ) {
			return new WP_REST_Response( $this->rpc_error( null, -32700, 'Parse error' ), 400 );
		}

		// Batch?
		$is_batch = $this->is_list_array( $dec );
		if ( $is_batch ) {
			$out = [];
			foreach ( $dec as $msg ) {
				$res = $this->handle_single_message( $msg );
				if ( null !== $res ) {
					$out[] = $res;
				}
			}
			// If all were notifications → 202 Accepted with no body.
			if ( empty( $out ) ) {
				return new WP_REST_Response( null, 202 );
			}
			return new WP_REST_Response( $out, 200 );
		}

		// Single message.
		$res = $this->handle_single_message( $dec );

		return new WP_REST_Response( $res, $res ? 200 : 202 );
	}

	/**
	 * Handle a single JSON-RPC message.
	 *
	 * @param array $msg
	 * @return array|null JSON-RPC response or null for notifications.
	 */
	protected function handle_single_message( array $msg ) {

		$id     = $msg['id'] ?? null;
		$method = isset( $msg['method'] ) ? (string) $msg['method'] : null;

		if ( ! $method ) {
			return $this->rpc_error( $id, -32600, 'Invalid Request' );
		}

		switch ( $method ) {

			case 'initialize':
				return $this->handle_initialize( $id, $msg );

			case 'notifications/initialized':
				return null;

			case 'tools/list':
				return $this->handle_tools_list( $id, $msg );

			case 'tools/call':
				return $this->handle_tools_call( $id, $msg );

			default:
				return apply_filters(
					'jet-engine/mcp-controller/handle-method',
					$this->rpc_error( $id, -32601, 'Method not found' ),
					$id,
					$method,
					$msg,
					$this
				);
		}
	}

	/**
	 * Handle 'tools/call' method.
	 *
	 * @param mixed $id
	 * @param array $msg
	 * @return array
	 */
	public function handle_tools_call( $id, $msg ) {

		$params = isset( $msg['params'] ) && is_array( $msg['params'] ) ? $msg['params'] : [];
		$name   = isset( $params['name'] ) ? (string) $params['name'] : '';
		$args   = isset( $params['arguments'] ) && is_array( $params['arguments'] ) ? $params['arguments'] : [];

		if ( '' === $name ) {
			return $this->rpc_error( $id, -32602, 'Missing tool name' );
		}

		$result = $this->run_tool( $name, $args );

		if ( is_array( $result ) && array_key_exists( 'error', $result ) ) {

			$text = is_string( $result['error'] ) ? $result['error'] : 'Tool error';

			return $this->rpc_response( $id, [
				'content' => [ [ 'type' => 'text', 'text' => (string) $text ] ],
				'isError' => true,
			] );
		}

		// Return JSON string as text (MCP ToolResult requires a content array).
		$payload_text = is_string( $result ) ? $result : wp_json_encode( $result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		return $this->rpc_response( $id, [
			'content' => [ [ 'type' => 'text', 'text' => (string) $payload_text ] ],
			'isError' => false,
		] );
	}

	/**
	 * Handle 'tools/list' method.
	 *
	 * @param mixed $id
	 * @param array $msg
	 * @return array
	 */
	public function handle_tools_list( $id, $msg ) {

		$params = isset( $msg['params'] ) && is_array( $msg['params'] ) ? $msg['params'] : [];
		/**
		 * @todo Handle 'cursor' param for pagination when we'll add more tools.
		 */

		return $this->rpc_response( $id, [
			'tools'      => Registry::instance()->get_features_array( true ),
			//'nextCursor' => null,
		] );
	}

	/**
	 * Handle 'initialize' method.
	 *
	 * @param mixed $id
	 * @param array $msg
	 * @return array
	 */
	public function handle_initialize( $id, $msg ) {
		return $this->rpc_response( $id, [
			'protocolVersion' => $this->protocol_version,
			'capabilities'    => [
				'tools' => [ 'listChanged' => false ],
			],
			'serverInfo'      => [
				'name'    => $this->server_name,
				'version' => $this->server_version,
			],
		] );
	}

	/**
	 * Run a tool by name with arguments.
	 * Result on failure: [ 'error' => '...' ]
	 * Result on success:  mixed (array|string|scalar)
	 *
	 * @param string $name
	 * @param array  $args
	 * @return mixed
	 */
	protected function run_tool( string $name, array $args ) {

		$tool = Registry::instance()->get_feature( $name );

		if ( ! $tool ) {
			return [ 'error' => 'Tool not found' ];
		}

		return $tool->run( $args );
	}

	/**
	 * Ensure minimal MCP tool object shape. Returns null if invalid.
	 *
	 * @param array $tool
	 * @return array|null
	 */
	protected function coerce_tool_shape( array $tool ) {
		$name        = isset( $tool['name'] ) ? (string) $tool['name'] : '';
		$description = isset( $tool['description'] ) ? (string) $tool['description'] : '';
		$schema      = isset( $tool['inputSchema'] ) && is_array( $tool['inputSchema'] ) ? $tool['inputSchema'] : null;

		if ( '' === $name || ! $schema ) {
			return null;
		}

		return [
			'name'        => $name,
			'description' => $description,
			'inputSchema' => $schema,
		];
	}

	/**
	 * True if $arr is a "list" (0..n-1 integer keys). Backport for PHP < 8.1.
	 *
	 * @param array $arr
	 * @return bool
	 */
	protected function is_list_array( array $arr ): bool {
		if ( function_exists( 'array_is_list' ) ) {
			return array_is_list( $arr );
		}
		$expected = 0;
		foreach ( $arr as $k => $_ ) {
			if ( $k !== $expected ) {
				return false;
			}
			$expected++;
		}
		return true;
	}
}
