<?php

namespace Jet_Engine\Modules\Data_Stores\Forms;

use Jet_Engine\Modules\Data_Stores\Module;
use Jet_Form_Builder\Actions\Action_Handler;
use Jet_Form_Builder\Actions\Types\Base;
use Jet_Form_Builder\Exceptions\Action_Exception;

class Action extends Base {

	/**
	 * @return mixed
	 */
	public function get_id() {
		return 'add_to_data_store';
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return __( 'Add to Data Store', 'jet-engine' );
	}

	public function action_data() {
		return array(
			'data_stores' => $this->get_data_stores_list(),
		);
	}

	/**
	 * @return mixed
	 */
	public function visible_attributes_for_gateway_editor() {
		return array( 'data_store_slug', 'fields_map' );
	}

	/**
	 * @return mixed
	 */
	public function self_script_name() {
		return 'JetEngineAddToDataStore';
	}

	/**
	 * @return mixed
	 */
	public function editor_labels() {
		return array(
			'data_store_slug' => __( 'Content Type:', 'jet-engine' ),
			'fields_map'         => __( 'Item Status:', 'jet-engine' ),
		);
	}

	public function editor_labels_help() {
		return array(
			'fields_map'     => __( 'Select content type fields to save appropriate form fields into', 'jet-engine' ),
		);
	}

	/**
	 * @param array $request
	 * @param Action_Handler $handler
	 *
	 * @return void
	 * @throws Action_Exception
	 */
	public function do_action( array $request, Action_Handler $handler ) {

		if ( ! jet_engine()->modules->is_module_active( 'data-stores' ) ) {
			return;
		}

		$post_ids        = array();
		$field           = ! empty( $this->settings['field'] ) ? $this->settings['field'] : false;
		$data_store_slug = ! empty( $this->settings['slug'] ) ? $this->settings['slug'] : false;

		if ( isset( $request[ $field ] ) ) {
			$post_ids = (array) $request[ $field ];
		}

		$store_instance = Module::instance()->stores->get_store( $data_store_slug );

		if ( ! $store_instance ) {
			return;
		}

		foreach ( $post_ids as $id ) {
			$store_instance->get_type()->add_to_store( $data_store_slug, $id );
		}
	}

	public function recursive_parse_values( $source ) {
		if ( ! is_array( $source ) ) {
			return wp_specialchars_decode(
				\Jet_Form_Builder\Classes\Tools::sanitize_text_field( $source ),
				ENT_COMPAT
			);
		}

		$response = array();
		foreach ( $source as $item_name => $item_value ) {
			$response[ $item_name ] = $this->recursive_parse_values( $item_value );
		}

		return $response;
	}

	public function get_data_stores_list() {
		$results = array();
		$data_stores = Module::instance()->stores->get_stores();

		if ( empty( $data_stores ) ) {
			return $results;
		}


		foreach ( $data_stores as $store ) {
			if ( 'local-storage' === $store->get_arg( 'type' ) ) continue;

			$results[] = array(
				'value' => $store->get_slug(),
				'label' => $store->get_name(),
			);
		}

		return $results;
	}
}