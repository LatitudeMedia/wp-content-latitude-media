<?php
namespace Jet_Engine\Modules\Data_Stores\Query_Builder;

use Jet_Engine\Modules\Data_Stores\Module;

class Query_Editor extends \Jet_Engine\Query_Builder\Query_Editor\Base_Query {

	/**
	 * Query type ID
	 */
	public function get_id() {
		return Manager::instance()->slug;
	}

	/**
	 * Query type name
	 */
	public function get_name() {
		return __( 'Data Store Query', 'jet-engine' );
	}

	/**
	 * Returns Vue component name for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_name() {
		return 'jet-data-stores-query';
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return mixed|void
	 */
	public function editor_component_data() {

		$all_stores = Module::instance()->stores->get_stores();
		$stores     = array(
			array(
				'value' => '',
				'label' => __( 'Select store...', 'jet-engine' )
			)
		);

		$front_stores = array();

		foreach ( $all_stores as $store ) {
			$stores[] = array(
				'value' => $store->get_slug(),
				'label' => $store->get_name(),
			);

			if ( $store->get_type()->is_front_store() ) {
				$front_stores[] = $store->get_slug();
			}
		}

		return apply_filters( 'jet-engine/query-builder/types/data-stores-query/data', [
			'stores'       => $stores,
			'front_stores' => $front_stores,
		] );
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return false|string
	 */
	public function editor_component_template() {
		ob_start();
		include jet_engine()->modules->modules_path( 'data-stores/inc/query-builder/editor-template.php' );
		return ob_get_clean();
	}

	/**
	 * Returns Vue component template for the Query editor for the current type.
	 *
	 * @return string
	 */
	public function editor_component_file() {
		return jet_engine()->modules->modules_url( 'data-stores/inc/assets/js/admin/query-editor.js' );
	}
}
