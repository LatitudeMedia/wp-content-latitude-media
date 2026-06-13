<?php
namespace Jet_Engine\Query_Builder\MCP;

use \Jet_Engine\MCP_Tools\Registry;
use \Jet_Engine\Query_Builder\Manager;

class Tool_Add_Query {

	/**
	 * Constructor.
	 */
	public function __construct() {
		Registry::instance()->add_feature( 'add-query', [
			'type'               => 'tool',
			'label'              => 'Add Query',
			'description'        => 'Register a new Query for Crocoblock Query Builder and return data in a standardized format. You MUST ALWAYS include the query_args object when calling this tool. Requests without query_args are invalid and will be rejected. For query_type: "sql", query_args must contain a non-empty sql string. For other types, query_args must contain the appropriate query arguments (WP_Query-style for posts, terms, users, etc.). Do not call this tool if you cannot provide query_args. Ask for the missing details first. Valid examples. SQL: {"name":"Recent orders","query_type":"sql","query_args":{"sql":"SELECT * FROM wp_posts WHERE post_type=\'shop_order\' LIMIT 10"}}. Posts: {"name":"Latest posts","query_type":"posts","query_args":{"post_type":"post","posts_per_page":5}} Invalid example (will be rejected) - {"name":"Latest posts","query_type":"posts"} ← missing query_args. Macros supported: Use macros from the conversation context for contextual params (IDs, user, dates). Prefer macros everywhere possible to make queries dynamic. ',
			'input_schema'       => array(
				'name' => array(
					'type'        => 'string',
					'description' => 'The name of the Query.',
				),
				'query_type' => array(
					'type'        => 'string',
					'description' => 'The type of the Query. This determines how the query will be processed and what data it will return.',
					'enum'       => $this->get_query_types(),
				),
				'query_args' => array(
					'type'        => 'object',
					'description' => 'REQUIRED. The query arguments object. Calls without query_args are invalid and will be rejected. For query_type: "sql", query_args must be { "sql": "<non-empty SQL string>" }. For query_type: "posts", terms, users, or comments, query_args must mirror WP_Query-style arguments such as post_type, posts_per_page, orderby, order, tax_query, and meta_query. For query_type: "custom-content-type" (CCT), query_args must follow WP_Query-style for post types with CCT fields expressed via meta_query, for example { "post_type": "cct-slug/name", "meta_query": [ { "key": "cct_field", "value": "some_value" } ], "posts_per_page": 10, "orderby": "date", "order": "DESC" }. All other query types should also format query_args into WP_Query-style wherever possible. Valid examples include SQL: { "query_args": { "sql": "SELECT ID, post_title FROM wp_posts WHERE post_type=\'post\' LIMIT 5" } }, Posts: { "query_args": { "post_type": "post", "posts_per_page": 5, "orderby": "date" } }, and CCT: { "query_args": { "post_type": "cct-slug/name", "meta_query": [ { "key": "cct_field", "value": "some_value" } ] } }. Invalid example (will be rejected): { "query_args": {} }. Do not call this feature if you cannot provide query_args; ask for the missing details first. Important note - use macros from the conversation context for contextual params (IDs, user, dates). Prefer macros everywhere possible to make queries dynamic. Important - if any macros are used in the meta_query and this is date-related macros - preferred format for any returned date values is timestamp.',
				),
			),
			'output_schema'      => array(
				'success'   => array(
					'type'        => 'boolean',
					'description' => 'Indicates whether the Query was successfully created.',
				),
				'item_id'   => array(
					'type'        => 'integer',
					'description' => 'The ID of the newly created Query.',
				),
				'query_url' => array(
					'type'        => 'string',
					'description' => 'The URL to the settings page of the newly created Query in the WordPress admin.',
				),
				'notices'   => array(
					'type'        => 'array',
					'description' => 'Any notices or warnings generated during the creation of the Query.',
					'items'       => array( 'type' => 'string' ),
				),
				'next_tool' => array(
					'type'        => 'string',
					'description' => 'The identifier of the next recommended tool to use after this one, if any.',
				),
				'date_warning' => array(
					'type'        => 'string',
					'description' => 'Warning message if date-related values are used in the query.',
				),
			),
			'execute_callback'   => [ $this, 'callback' ],
		] );
	}

	/**
	 * Callback to execute the feature.
	 *
	 * @param array $input The input data for the feature.
	 */
	public function callback( $input = [] ) {

		if ( empty( $input['query_args'] ) ) {
			return new \WP_Error( 'invalid_input', 'query_args property are required' );
		}

		$name = ! empty( $input['name'] ) ? sanitize_text_field( $input['name'] ) : '';
		$slug = sanitize_title( $name );
		$type = ! empty( $input['query_type'] ) ? sanitize_text_field( $input['query_type'] ) : '';

		$query_args = array();

		// In some cases we caught the args was nested under the $type key, so check this first
		if ( isset( $input['query_args'][ $type ] ) && is_array( $input['query_args'][ $type ] ) ) {
			$query_args = $input['query_args'][ $type ];
		} elseif ( is_array( $input['query_args'] ) ) {
			$query_args = $input['query_args'];
		}

		$converter      = Controller::get_converter( $type );
		$converted_args = $converter ? $converter->convert( $query_args ) : [];
		$dynamic_key    = '__dynamic_' . $type;

		if ( isset( $converted_args[ $dynamic_key ] ) ) {
			$dynamic_args = $converted_args[ $dynamic_key ];
			unset( $converted_args[ $dynamic_key ] );
		} else {
			$dynamic_args = [];
		}

		$full_args = [
			'api_access'           => null,
			'api_access_cap'       => null,
			'api_access_role'      => [],
			'api_endpoint'         => false,
			'api_namespace'        => null,
			'api_path'             => null,
			'api_schema'           => [ [ 'arg' => '', 'value' => '' ] ],
			'avoid_duplicates'     => false,
			'cache_expires'        => 0,
			'cache_query'          => true,
			'description'          => '',
			'name'                 => $name,
			$type                  => $converted_args,
			$dynamic_key           => $dynamic_args,
			'preview_page'         => null,
			'preview_page_title'   => null,
			'preview_query_string' => null,
			'query_id'             => null,
			'query_type'           => $type,
			'show_preview'         => false
		];

		// Ensure all query types are registered
		do_action( 'jet-engine/query-builder/editor/types' );

		Manager::instance()->data->set_request( [
			'name'        => $name,
			'slug'        => $slug,
			'args'        => $full_args,
			'meta_fields' => [],
		] );

		$item_id = Manager::instance()->data->create_item( false );
		$q_url   = '';

		if ( ! empty( $item_id ) ) {
			$q_url = admin_url( 'admin.php?page=jet-engine-query&query_action=edit&id=' . $item_id );
		}

		return array(
			'success'   => ! empty( $item_id ),
			'item_id'   => $item_id,
			'query_url' => $q_url,
			'notices'   => Manager::instance()->get_notices(),
			'next_tool' => 'tool/add-listing',
			'date_warning' => 'If any query argument includes a date value or date macro, please verify that the stored date format matches the macro\'s output and that the resulting dates are correct. Important - preferred format of date storing for any date operations is timestamp.'
		);
	}

	/**
	 * Get the available query types.
	 *
	 * @return array
	 */
	public function get_query_types() {

		Manager::instance()->include_factory();
		\Jet_Engine\Query_Builder\Query_Factory::ensure_queries();

		return \Jet_Engine\Query_Builder\Query_Factory::get_query_types();
	}

	/**
	 * Merge query types for query property.
	 *
	 * @param array $defaults Default described query types
	 * @return array
	 */
	public function merge_query_types_for_props( $defaults ) {

		$query_types = $this->get_query_types();

		foreach ( $query_types as $type ) {
			if ( ! isset( $defaults[ $type ] ) ) {
				$query_class = \Jet_Engine\Query_Builder\Query_Factory::get_query_class( $type );

				if (
					class_exists( $query_class )
					&& is_callable( [ $query_class, 'mcp_description' ] )
				) {
					$defaults[ $type ] = call_user_func( [ $query_class, 'mcp_description' ] );
				} else {
					$defaults[ $type ] = array(
						'type'        => 'object',
						'description' => sprintf( __( '%1$s query arguments in the format supported by %1$s.', 'jet-engine' ), ucfirst( str_replace( [ '_', '-' ], ' ', $type ) ) ),
					);
				}
			}
		}

		return $defaults;
	}
}
