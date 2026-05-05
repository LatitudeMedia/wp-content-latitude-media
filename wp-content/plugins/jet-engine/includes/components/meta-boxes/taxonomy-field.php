<?php
/**
 * Taxonomy field runtime support for Meta Boxes.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Meta_Boxes_Taxonomy_Field' ) ) {

	class Jet_Engine_Meta_Boxes_Taxonomy_Field {

		public function __construct() {
			add_filter( 'jet-engine/meta-fields/taxonomy/args', array( $this, 'prepare_field_args' ), 10, 3 );
			add_filter( 'cx_post_meta/pre_get_meta', array( $this, 'maybe_load_terms' ), 10, 5 );
		}

		/**
		 * Prepare control args for taxonomy field.
		 *
		 * @param array              $result Current prepared field args.
		 * @param array              $field  Source field settings.
		 * @param Jet_Engine_CPT_Meta $meta  Runtime meta object.
		 *
		 * @return array
		 */
		public function prepare_field_args( $result, $field, $meta ) {

			$taxonomy = ! empty( $field['taxonomy'] ) ? sanitize_key( $field['taxonomy'] ) : '';

			$result['jet_engine_field_type'] = 'taxonomy';
			$result['taxonomy']              = $taxonomy;
			$result['load_terms']            = ! empty( $field['load_terms'] ) && filter_var( $field['load_terms'], FILTER_VALIDATE_BOOLEAN );
			$result['save_terms']            = ! empty( $field['save_terms'] ) && filter_var( $field['save_terms'], FILTER_VALIDATE_BOOLEAN );
			$result['return_value']          = ! empty( $field['return_value'] ) ? sanitize_key( $field['return_value'] ) : 'term_id';

			if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
				$result['type']        = 'select';
				$result['options']     = array();
				$result['description'] = $this->append_description(
					isset( $result['description'] ) ? $result['description'] : '',
					esc_html__( 'Selected taxonomy is not registered. Field is shown without options.', 'jet-engine' )
				);

				return $result;
			}

			$appearance = ! empty( $field['appearance'] ) ? sanitize_key( $field['appearance'] ) : 'select';
			$appearance = in_array( $appearance, array( 'select', 'radio', 'checkboxes' ), true ) ? $appearance : 'select';
			$multiple   = $this->is_multiple( $field, $appearance );

			$allow_null   = ! empty( $field['allow_null'] ) && filter_var( $field['allow_null'], FILTER_VALIDATE_BOOLEAN );
			$return_value = $result['return_value'];

			if ( 'radio' === $appearance ) {
				$result['type'] = 'radio';
			} elseif ( 'checkboxes' === $appearance ) {
				$result['type']     = 'checkbox';
				$result['is_array'] = true;
				$result['multiple'] = true;
			} else {
				$result['type'] = 'select';

				if ( $multiple ) {
					$result['multiple'] = true;
				}
			}

			if ( in_array( $appearance, array( 'radio', 'checkboxes' ), true ) && ! Jet_Engine_Tools::is_empty( $field, 'check_radio_layout' ) ) {
				$result['layout'] = $field['check_radio_layout'];
			}

			// Load terms only at actual control render time.
			$result['options']          = array();
			$result['options_callback'] = function() use ( $field, $taxonomy, $return_value, $appearance, $multiple, $allow_null ) {
				return $this->get_control_options( $field, $taxonomy, $return_value, $appearance, $multiple, $allow_null );
			};

			$result['appearance'] = $appearance;
			$result['multiple']   = $multiple;

			// Keep REST type inference deterministic.
			$result['sanitize_callback'] = array( $this, 'sanitize_field_value' );

			return $result;

		}

		/**
		 * Build rendered control options for taxonomy field.
		 *
		 * @param array  $field        Source field settings.
		 * @param string $taxonomy     Taxonomy slug.
		 * @param string $return_value Return value format.
		 * @param string $appearance   Current appearance.
		 * @param bool   $multiple     Multiple mode.
		 * @param bool   $allow_null   Allow empty value.
		 *
		 * @return array
		 */
		public function get_control_options( $field, $taxonomy, $return_value, $appearance, $multiple, $allow_null ) {

			$options = $this->get_options_for_field( $field, $taxonomy, $return_value );

			if ( 'radio' === $appearance ) {
				$options = $this->prepare_radio_options( $options );

				if ( $allow_null && ! $multiple ) {
					$options = array(
						'' => array(
							'label' => esc_html__( 'No term', 'jet-engine' ),
						),
					) + $options;
				}

				return $options;
			}

			$options = $this->prepare_select_options( $options );

			if ( $allow_null && ! $multiple && 'select' === $appearance ) {
				$options = array( '' => esc_html__( 'Select term...', 'jet-engine' ) ) + $options;
			}

			return $options;

		}

		/**
		 * Prefill value from assigned terms when meta is empty.
		 *
		 * @param mixed  $pre     Pre value.
		 * @param object $post    Post object.
		 * @param string $key     Field name.
		 * @param mixed  $default Default value.
		 * @param array  $field   Field config.
		 *
		 * @return mixed
		 */
		public function maybe_load_terms( $pre, $post, $key, $default, $field ) {

			if ( false !== $pre ) {
				return $pre;
			}

			if ( empty( $field['jet_engine_field_type'] ) || 'taxonomy' !== $field['jet_engine_field_type'] ) {
				return $pre;
			}

			if ( empty( $field['load_terms'] ) || ! is_object( $post ) ) {
				return $pre;
			}

			$taxonomy = ! empty( $field['taxonomy'] ) ? sanitize_key( $field['taxonomy'] ) : '';

			if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
				return $pre;
			}

			$stored_value = get_post_meta( $post->ID, $key, true );

			if ( '' !== $stored_value && null !== $stored_value && array() !== $stored_value ) {
				return $pre;
			}

			$terms = wp_get_object_terms(
				$post->ID,
				$taxonomy,
				array(
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				return $pre;
			}

			$return_value = ! empty( $field['return_value'] ) ? sanitize_key( $field['return_value'] ) : 'term_id';
			$multiple     = ! empty( $field['multiple'] ) && filter_var( $field['multiple'], FILTER_VALIDATE_BOOLEAN );
			$values       = array();

			foreach ( $terms as $term ) {
				$values[] = Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper::get_term_value( $term, $return_value );
			}

			if ( $multiple ) {
				return $values;
			}

			return isset( $values[0] ) ? $values[0] : $pre;

		}

		/**
		 * Sanitize taxonomy field value for storage.
		 *
		 * @param mixed  $value Raw value.
		 * @param string $key   Field key.
		 * @param array  $field Field config.
		 *
		 * @return mixed
		 */
		public function sanitize_field_value( $value, $key, $field ) {

			$return_value = ! empty( $field['return_value'] ) ? sanitize_key( $field['return_value'] ) : 'term_id';
			$multiple     = ! empty( $field['multiple'] ) && filter_var( $field['multiple'], FILTER_VALIDATE_BOOLEAN );
			$values       = Jet_Engine_Meta_Boxes_Taxonomy_Values_Helper::normalize_request_values( $value );
			$sanitized    = array();

			foreach ( $values as $raw_value ) {
				if ( 'term_id' === $return_value ) {
					$raw_value = absint( $raw_value );

					if ( ! $raw_value ) {
						continue;
					}
				} else {
					$raw_value = sanitize_text_field( $raw_value );

					if ( '' === $raw_value ) {
						continue;
					}
				}

				$sanitized[] = $raw_value;
			}

			if ( $multiple ) {
				return array_values( $sanitized );
			}

			return isset( $sanitized[0] ) ? $sanitized[0] : '';

		}

		/**
		 * Determine if field should accept multiple values.
		 *
		 * @param array  $field      Field settings.
		 * @param string $appearance Current appearance.
		 *
		 * @return bool
		 */
		public function is_multiple( $field, $appearance ) {

			if ( 'checkboxes' === $appearance ) {
				return true;
			}

			$value = null;

			if ( isset( $field['multiple'] ) ) {
				$value = $field['multiple'];
			} elseif ( isset( $field['is_multiple'] ) ) {
				$value = $field['is_multiple'];
			}

			return ! is_null( $value ) && filter_var( $value, FILTER_VALIDATE_BOOLEAN );

		}

		/**
		 * Build options map for current field.
		 *
		 * @param array  $field        Field settings.
		 * @param string $taxonomy     Taxonomy slug.
		 * @param string $return_value Return value mode.
		 *
		 * @return array
		 */
		public function get_options_for_field( $field, $taxonomy, $return_value ) {

			$args  = Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper::prepare_terms_args(
				$field,
				$taxonomy,
				array(
					'orderby' => array( 'terms_orderby', 'orderby' ),
					'order'   => array( 'terms_order', 'order' ),
				)
			);
			$terms = get_terms( $args );
			$items = array();

			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				return $items;
			}

			foreach ( $terms as $term ) {
				$items[ Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper::get_term_value( $term, $return_value ) ] = Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper::get_term_label( $term );
			}

			return $items;

		}

		/**
		 * Convert associative options to radio control format.
		 *
		 * @param array $options Value => Label options.
		 *
		 * @return array
		 */
		public function prepare_radio_options( $options ) {

			$result = array();

			foreach ( $options as $value => $label ) {
				$result[ $value ] = array(
					'label' => $label,
				);
			}

			return $result;

		}

		/**
		 * Convert associative options to select/checkbox format.
		 *
		 * @param array $options Value => Label options.
		 *
		 * @return array
		 */
		public function prepare_select_options( $options ) {
			return $options;
		}

		/**
		 * Append warning text to existing field description.
		 *
		 * @param string $description Existing description.
		 * @param string $to_add      Message to append.
		 *
		 * @return string
		 */
		public function append_description( $description, $to_add ) {

			$description = trim( wp_strip_all_tags( (string) $description ) );
			$to_add      = trim( (string) $to_add );

			if ( ! $description ) {
				return $to_add;
			}

			return $description . ' ' . $to_add;

		}

	}
}
