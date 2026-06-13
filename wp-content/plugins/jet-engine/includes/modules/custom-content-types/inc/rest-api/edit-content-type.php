<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Rest;

use Jet_Engine\Modules\Custom_Content_Types\Module;

class Edit_Content_Type extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'edit-content-type';
	}

	public function safe_get( $args = array(), $group = '', $key = '', $default = false ) {
		return isset( $args[ $group ][ $key ] ) ? $args[ $group ][ $key ] : $default;
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		$params = $request->get_params();

		if ( empty( $params['id'] ) ) {

			Module::instance()->manager->add_notice(
				'error',
				__( 'Item ID not found in request', 'jet-engine' )
			);

			return rest_ensure_response( array(
				'success' => false,
				'notices' => Module::instance()->manager->get_notices(),
			) );

		}

		$slug        = ! empty( $params['general_settings']['slug'] ) ? $params['general_settings']['slug'] : '';
		$meta_fields = ! empty( $params['meta_fields'] ) ? $params['meta_fields'] : array();
		$args        = ! empty( $params['general_settings'] ) ? $params['general_settings'] : array();

		Module::instance()->manager->data->set_request( array(
			'id'          => $params['id'],
			'name'        => ! empty( $params['general_settings']['name'] ) ? $params['general_settings']['name'] : '',
			'slug'        => $slug,
			'args'        => $args,
			'meta_fields' => $meta_fields,
		) );

		/**
		 * @var \Jet_Engine\Modules\Custom_Content_Types\Data
		 */
		$data = Module::instance()->manager->data;

		$updated = $data->edit_item( false );

		$meta_fields = array_merge(
			$meta_fields,
			$data->get_service_fields( $args )
		);

		$db = new \Jet_Engine\Modules\Custom_Content_Types\DB( $slug, $data->get_sql_columns_from_fields( $meta_fields ) );

		$table_notice = false;

		$table_exists = \Jet_Engine\Modules\Custom_Content_Types\DB::custom_table_exists( $slug );
		$schema_valid = $db->has_columns_by_schema();

		if ( ! $table_exists ) {
			$table_notice = esc_html__( 'DB table does not exist. Please, check the field names for MySQL reserved words and update this CCT.', 'jet-engine' );
		} elseif( ! $schema_valid ) {
			$table_notice = esc_html__( 'DB schema does not correspond to CCT settings. Please, check the field names for MySQL reserved words and update this CCT.', 'jet-engine' );
		}

		return rest_ensure_response( array(
			'success'      => $updated && $table_exists && $schema_valid,
			'notices'      => Module::instance()->manager->get_notices(),
			'table_notice' => $table_notice,
		) );

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
	public function permission_callback( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get query param. Regex with query parameters
	 *
	 * @return string
	 */
	public function get_query_params() {
		return '(?P<id>[a-z\-\d]+)';
	}

}