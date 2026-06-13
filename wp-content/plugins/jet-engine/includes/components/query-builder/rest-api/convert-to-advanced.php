<?php
namespace Jet_Engine\Query_Builder\Rest;

use Jet_Engine\Query_Builder\Manager;

class Convert_To_Advanced extends \Jet_Engine_Base_API_Endpoint {

	/**
	 * Returns route name
	 *
	 * @return string
	 */
	public function get_name() {
		return 'convert-query-advanced';
	}

	/**
	 * API callback
	 *
	 * @return void
	 */
	public function callback( $request ) {

		Manager::instance()->include_factory();

		$params = $request->get_params();
		$type = ! empty( $params['query_type'] ) ? $params['query_type'] : false;

		if ( ! $type || $type !== 'sql' ) {
			return rest_ensure_response( array(
				'success' => false,
				'data'    => null,
			) );
		}

		$factory = new \Jet_Engine\Query_Builder\Query_Factory( array(
			'lables' => array( 'name' => 'Preview' ),
			'args'   => array(
				'query_type'         => $type,
				$type                => $params['query'],
				'__dynamic_' . $type => $params['dynamic_query'],
			),
		) );

		/**
		 * @var \Jet_Engine\Query_Builder\Queries\SQL_Query
		 */
		$query = $factory->get_query();

		if ( ! $query ) {
			return rest_ensure_response( array(
				'success' => false,
				'data'    => __( 'Can`t find the query object', 'jet-engine' ),
			) );
		}

		$result = array(
			'success' => true,
			'data'    => $query->get_converted_sql(),
		);

		return rest_ensure_response( $result );

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

}
