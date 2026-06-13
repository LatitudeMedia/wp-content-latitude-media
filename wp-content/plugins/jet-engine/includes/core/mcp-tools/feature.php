<?php
namespace Jet_Engine\MCP_Tools;

class Feature {

	/**
	 * The unique identifier for the feature.
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * The type of the feature (e.g., 'tool', 'resource', etc.).
	 *
	 * @var string
	 */
	protected $type = 'tool';

	/**
	 * The human-readable ability label.
	 *
	 * @var string
	 */
	protected $label;

	/**
	 * The detailed ability description.
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * The optional ability input schema.
	 *
	 * @var array<string,mixed>
	 */
	protected $input_schema = array();

	/**
	 * The optional ability output schema.
	 *
	 * @var array<string,mixed>
	 */
	protected $output_schema = array();

	/**
	 * The ability execute callback.
	 *
	 * @var callable( array<string,mixed> $input): (mixed|\WP_Error)
	 */
	protected $execute_callback;

	/**
	 * The optional ability permission callback.
	 *
	 * @var ?callable( array<string,mixed> $input ): (bool|\WP_Error)
	 */
	protected $permission_callback = null;

	/**
	 * The optional ability metadata.
	 *
	 * @var array<string,mixed>
	 */
	protected $meta = array();

	/**
	 * Constructor.
	 *
	 * @param string $id The unique identifier for the feature.
	 * @param array  $args {
	 *     Optional. An array of arguments to configure the feature.
	 *
	 *     @type string   $label               The human-readable ability label.
	 *     @type string   $description         The detailed ability description.
	 *     @type array    $input_schema        The optional ability input schema.
	 *     @type array    $output_schema       The optional ability output schema.
	 *     @type callable $execute_callback    The ability execute callback.
	 *     @type callable $permission_callback The optional ability permission callback.
	 *     @type array    $meta                The optional ability metadata.
	 * }
	 */
	public function __construct( $id, $args = [] ) {

		$this->id = $id;
		$this->type = $args['type'] ?? 'tool';
		$this->label = $args['label'] ?? '';
		$this->description = $args['description'] ?? '';
		$this->input_schema = $args['input_schema'] ?? [];
		$this->output_schema = $args['output_schema'] ?? [];
		$this->execute_callback = $args['execute_callback'] ?? null;
		$this->permission_callback = $args['permission_callback'] ?? null;
		$this->meta = $args['meta'] ?? [];
	}

	/**
	 * Get the full ID of the feature.
	 *
	 * @return string
	 */
	function get_name() {
		return $this->get_type() . '-' . $this->get_id();
	}

	/**
	 * Get the ID of the feature.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the name of the feature.
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get the description of the feature.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Callback to execute the feature.
	 *
	 * @param array $input The input data for the feature.
	 */
	public function run( $input = [] ) {

		if ( ! is_callable( $this->execute_callback ) ) {
			return new \WP_Error(
				'feature_no_execute',
				esc_html__( 'This feature cannot be executed.', 'jet-engine' )
			);
		}

		return call_user_func( $this->execute_callback, $input );
	}

	/**
	 * Returns endpoint request method - GET/POST/PUT/DELTE
	 *
	 * @return string
	 */
	public function get_method() {
		return 'POST';
	}

	/**
	 * Check user access to current end-popint
	 *
	 * @return bool
	 */
	public function check_permissions( $request ) {

		// Locked by default to avoid accidental unauthorized access
		if ( ! is_callable( $this->permission_callback ) ) {
			return current_user_can( 'manage_options' );
		}

		return call_user_func( $this->permission_callback, $request );
	}

	/**
	 * Get the type of the feature.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Get the input schema for the feature.
	 *
	 * @return array
	 */
	public function get_input_schema() {
		return [
			'type'       => 'object',
			'properties' => $this->input_schema,
		];
	}

	/**
	 * Get the output schema for the feature.
	 *
	 * @return array
	 */
	public function get_output_schema() {
		return $this->output_schema;
	}

	/**
	 * Get the metadata for the feature.
	 *
	 * @return array
	 */
	public function get_meta() {
		return $this->meta;
	}

	/**
	 * Convert the feature to an array representation.
	 *
	 * @param bool $as_mcp_tools Whether to format the output as MCP tools or just as plain array.
	 * @return array
	 */
	public function to_array( $as_mcp_tools = false ) {

		if ( $as_mcp_tools ) {
			return array(
				'name'         => $this->get_name(),
				'description'  => $this->get_description(),
				'inputSchema'  => $this->get_input_schema(),
				'meta'         => $this->get_meta(),
			);
		}

		return array(
			'id'           => $this->get_name(),
			'name'         => $this->get_name(),
			'label'        => $this->get_label(),
			'description'  => $this->get_description(),
			'type'         => $this->get_type(),
			'input_schema' => $this->get_input_schema(),
			'output_schema'=> $this->get_output_schema(),
			'meta'         => $this->get_meta(),
		);
	}
}