<?php
/**
 * Manager for allowed option sources
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Meta_Boxes_Option_Source class
 */
class Jet_Engine_Meta_Boxes_Option_Sources {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	public $meta_fields = [];

	public function __construct() {

		require_once jet_engine()->meta_boxes->component_path( 'fields-options/manual-options.php' );
		require_once jet_engine()->meta_boxes->component_path( 'fields-options/manual-bulk-options.php' );
		require_once jet_engine()->meta_boxes->component_path( 'fields-options/taxonomy-options.php' );

		new \Jet_Engine\Meta_Boxes\Option_Sources\Manual_Options();
		new \Jet_Engine\Meta_Boxes\Option_Sources\Manual_Bulk_Options();
		new \Jet_Engine\Meta_Boxes\Option_Sources\Taxonomy_Options();

		do_action( 'jet-engine/meta-boxes/init-options-sources' );

		add_action( 'init', [ $this, 'add_hooks_to_save_custom_values' ], 9999 );

	}

	/**
	 * Find meta fields with enabling `save custom` option
	 *
	 * @param $object_type
	 * @param $sub_type
	 * @param $fields
	 * @param $item_id
	 * @param $data_handler
	 * @param $is_built_in
	 */
	public function find_meta_fields_with_save_custom( $object_type, $sub_type, $fields, $item_id, $data_handler, $is_built_in = false ) {

		foreach ( $fields as $field ) {

			if ( empty( $field['object_type'] ) ) {
				continue;
			}

			if ( 'field' !== $field['object_type'] || ! in_array( $field['type'], array( 'checkbox', 'radio' ) ) ) {
				continue;
			}

			$allow_custom = ! empty( $field['allow_custom'] ) && filter_var( $field['allow_custom'], FILTER_VALIDATE_BOOLEAN );
			$save_custom  = ! empty( $field['save_custom'] ) && filter_var( $field['save_custom'], FILTER_VALIDATE_BOOLEAN );

			if ( ! $allow_custom || ! $save_custom ) {
				continue;
			}

			$data_class = get_class( $data_handler );

			if ( empty( $this->meta_fields[ $data_class ] ) ) {
				$this->meta_fields[ $data_class ] = [
					'data_handler' => $data_handler,
					'fields'       => [],
				];
			}

			if ( empty( $this->meta_fields[ $data_class ]['fields'][ $object_type ] ) ) {
				$this->meta_fields[ $data_class ]['fields'][ $object_type ] = [];
			}

			$field_args = array_merge( array( 'item_id' => $item_id ), $field );

			if ( in_array( $object_type, array( 'post', 'taxonomy' ) ) ) {
				$field_args['is_built_in'] = $is_built_in;
			}

			if ( empty( $this->meta_fields[ $data_class ]['fields'][ $object_type ][ $sub_type ] ) ) {
				$this->meta_fields[ $data_class ]['fields'][ $object_type ][ $sub_type ] = [];
			}

			$this->meta_fields[ $data_class ]['fields'][ $object_type ][ $sub_type ][ $field['name'] ] = $field_args;

		}
	}

	/**
	 * Add hooks to save custom values
	 */
	public function add_hooks_to_save_custom_values() {

		if ( empty( $this->meta_fields ) ) {
			return;
		}

		foreach ( $this->meta_fields as $fields_set ) {

			foreach ( $fields_set['fields'] as $object_type => $sub_types ) {

				$data_handler = $fields_set['data_handler'];

				switch ( $object_type ) {

					case 'post':

						foreach ( $sub_types as $post_type => $fields ) {

							add_action( "save_post_{$post_type}", function( $id ) use ( $data_handler, $post_type ) {
								$this->save_custom_values( $id, $data_handler, 'post', $post_type );
							} );
						}

						break;

					case 'tax':
					case 'taxonomy':
						foreach ( $sub_types as $tax => $fields ) {
							
							add_action( "created_{$tax}", function( $id ) use ( $data_handler, $tax ) {
								$this->save_custom_values( $id, $data_handler, 'taxonomy', $tax );
							} );

							add_action( "edited_{$tax}", function( $id ) use ( $data_handler, $tax ) {
								$this->save_custom_values( $id, $data_handler, 'taxonomy', $tax );
							} );

						}
						break;

					case 'user':

						add_action( 'edit_user_profile_update', function( $id ) use ( $data_handler ) {
							$this->save_custom_values( $id, $data_handler, 'user', 'user' );
						} );

						add_action( 'personal_options_update',  function( $id ) use ( $data_handler ) {
							$this->save_custom_values( $id, $data_handler, 'user', 'user' );
						} );

						break;

					case 'options':

						foreach ( $sub_types as $page_slug => $fields ) {
							
							$hook_name = 'jet-engine/options-pages/after-save/' . $page_slug;
							add_action( $hook_name, function( $page ) use ( $data_handler, $page_slug ) {
								$this->save_custom_values( $page->page['id'], $data_handler, 'options', $page_slug );
							} );
						}

						break;

					default:
						do_action( 'jet-engine/meta-boxes/hook-save-custom/' . $object_type, $sub_types, $this );
						break;
				}
			}
		}

	}

	/**
	 * Save custom values
	 *
	 * @param $id Object ID
	 */
	public function save_custom_values( $id, $data_handler, $object_type = false, $sub_type = false ) {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! $object_type || ! $sub_type ) {
			return;
		}

		if ( empty( $_POST ) ) {
			return;
		}

		switch ( $object_type ) {

			case 'post':
				if ( ! current_user_can( 'edit_post', $id ) ) {
					return;
				}
				break;

			case 'taxonomy':
				if ( ! current_user_can( 'edit_term', $id ) ) {
					return;
				}
				break;

			case 'user':
				if ( ! current_user_can( 'edit_user', $id ) ) {
					return;
				}
				break;

			default:
				
				$is_valid = apply_filters( 
					'jet-engine/meta-boxes/save-custom-value/validate/' . $object_type,
					true,
					$id,
					$data_handler,
					$sub_type
				);

				if ( ! $is_valid ) {
					return;
				}

				break;
		}

		$data_class = get_class( $data_handler );
		$fields     = $this->meta_fields[ $data_class ]['fields'][ $object_type ][ $sub_type ];
		$update     = false;

		$to_update = array();

		foreach ( $this->meta_fields[ $data_class ]['fields'][ $object_type ][ $sub_type ] as $field => $field_args ) {
			if ( ! isset( $_POST[ $field ] ) || '' === $_POST[ $field ] ) {
				continue;
			}

			$to_update[ $field_args['item_id'] ] = true;
		}

		foreach ( array_keys( $to_update ) as $item_id ) {
			$item         = $data_handler->get_item_for_edit( $item_id );
			$_meta_fields = isset( $item['meta_fields'] ) ? $item['meta_fields'] : ( isset( $item['fields'] ) ? $item['fields'] : [] );

			$is_built_in = false;

			foreach ( $this->meta_fields[ $data_class ]['fields'][ $object_type ][ $sub_type ] as $field => $field_args ) {
				$_meta_fields = $this->maybe_add_custom_values_to_options( $_meta_fields, $field, $field_args );

				if ( in_array( $object_type, array( 'post', 'taxonomy' ) ) && isset( $field_args['is_built_in'] ) ) {
					$is_built_in = $field_args['is_built_in'];
				}
			}

			if ( $is_built_in ) {
				$data_handler->query_args['status'] = 'built-in';
			}

			$data_handler->set_request( array_merge(
				$item,
				array(
					'id'          => $item_id,
					'args'        => ( ! empty( $item['general_settings'] ) ) ? $item['general_settings'] : ( ! empty( $item['args'] ) ? $item['args'] : [] ),
					'meta_fields' => $_meta_fields,
				),
				isset( $item['general_settings'] ) ? $item['general_settings'] : [],
				isset( $item['advanced_settings'] ) ? $item['advanced_settings'] : [],
				isset( $item['labels'] ) ? $item['labels'] : []
			) );

			$data_handler->update_item_in_db( array_merge(
				[ 'id' => $item_id ],
				$data_handler->sanitize_item_from_request( $is_built_in )
			) );

			// Update `meta_fields` storage.
			// Solution for correct calculation indexers.
			if ( in_array( $object_type, [ 'post', 'taxonomy' ] ) ) {
				$found_new_field = false;

				foreach ( $_meta_fields as $_meta_field ) {
					if ( ! empty( $_meta_field['name'] ) && $field === $_meta_field['name'] ) {
						$found_new_field = $_meta_field;
					}
				}

				if ( ! empty( $found_new_field ) ) {
					foreach ( jet_engine()->meta_boxes->meta_fields[ $sub_type ] as $index => $_field ) {
						if ( ! empty( $_field['name'] ) && $field === $_field['name'] ) {
							jet_engine()->meta_boxes->meta_fields[ $sub_type ][ $index ] = $found_new_field;
						}
					}
				}
			}
			
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

	}

	/**
	 * Maybe add custom values to options.
	 *
	 * @param $meta_fields
	 * @param $field
	 * @param $field_args
	 *
	 * @return mixed
	 */
	public function maybe_add_custom_values_to_options( $meta_fields, $field, $field_args ) {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$update_meta  = false;
		$meta_index   = array_search( $field, array_column( $meta_fields, 'name' ) );
		$post_meta    = new \Jet_Engine_CPT_Meta();
		$meta_options = $post_meta->filter_options_list( [], $meta_fields[ $meta_index ] );
		$meta_options = ( ! empty( $meta_options ) && is_array( $meta_options ) ) ? array_column( $meta_options, 'key' ) : [];

		switch ( $field_args['type'] ) {
			case 'checkbox':

				if ( ! isset( $_POST[ $field ] ) || ! is_array( $_POST[ $field ] ) ) {
					return $meta_fields;
				}

				$raw_values = wp_unslash( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized  -- sanitized below
				$custom_values = array_diff( array_keys( $raw_values ), $meta_options );

				if ( ! empty( $custom_values ) ) {
					foreach ( $custom_values as $custom_value ) {

						if ( isset( $raw_values[ $custom_value ] ) ) {
							$custom_item_value = filter_var(
								$raw_values[ $custom_value ],
								FILTER_VALIDATE_BOOLEAN
							);
						}

						if ( $custom_item_value ) {
							$meta_fields[ $meta_index ] = $this->get_field_with_merged_options(
								$meta_fields[ $meta_index ],
								sanitize_text_field( $custom_value )
							);

							$update_meta = true;
						}
					}
				}
				break;

			case 'radio':

				if ( ! isset( $_POST[ $field ] ) ) {
					return $meta_fields;
				}

				$raw_value    = wp_unslash( $_POST[ $field ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized  -- sanitized below
				$custom_value = ! in_array( $raw_value, $meta_options, true ) ? $raw_value : false;

				if ( ! Jet_Engine_Tools::is_empty( $custom_value ) ) {

					$meta_fields[ $meta_index ] = $this->get_field_with_merged_options( 
						$meta_fields[ $meta_index ],
						sanitize_text_field( $custom_value )
					);

					$update_meta = true;
				}

				break;
		}

		if ( ! $update_meta ) {
			return $meta_fields;
		}

		return $meta_fields;
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	/**
	 * Returns meta field with custom value merged options.
	 * It's only wrapper method. Implementation depends on options source
	 * 
	 * @param  [type] $field        [description]
	 * @param  [type] $custom_value [description]
	 * @return [type]               [description]
	 */
	public function get_field_with_merged_options( $field, $custom_value ) {
		return apply_filters( 'jet-engine/meta-boxes/option-sources/get-merged-options', $field, $custom_value );
	}

	/**
	 * Returns list of allowed option sources
	 * 
	 * @return [type] [description]
	 */
	public function get_allowed_sources() {
		return apply_filters( 'jet-engine/meta-boxes/option-sources', [
			'manual'      => __( 'Manual Input', 'jet-engine' ),
			'manual_bulk' => __( 'Bulk Manual Input', 'jet-engine' ),
			'taxonomy'    => __( 'Taxonomy', 'jet-engine' ),
			'glossary'    => __( 'Glossary', 'jet-engine' ),
			'query'       => __( 'Query Builder', 'jet-engine' ),
		] );
	}

	/**
	 * Returns list of allowed option sources adated to use in JS components
	 * 
	 * @return [type] [description]
	 */
	public function get_allowed_sources_for_js() {
		
		$result = [];

		foreach ( $this->get_allowed_sources() as $value => $label ) {
			$result[] = [
				'value' => $value,
				'label' => $label,
			];
		}

		return $result;

	}

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return static
	 */
	public static function instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

}
