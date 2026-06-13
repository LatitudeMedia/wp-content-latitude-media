<?php
/**
 * Sync taxonomy relationships for taxonomy field type.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Meta_Boxes_Taxonomy_Terms_Sync' ) ) {

	class Jet_Engine_Meta_Boxes_Taxonomy_Terms_Sync {

		public function __construct() {
			add_action( 'save_post', array( $this, 'sync_terms' ), 20, 2 );
		}

		/**
		 * Sync terms for submitted taxonomy fields.
		 *
		 * @param int     $post_id Post ID.
		 * @param \WP_Post $post    Current post object.
		 *
		 * @return void
		 */
		public function sync_terms( $post_id, $post ) {

			if ( empty( $_POST ) || ! $post || ! is_object( $post ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
				return;
			}

			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			$post_type = get_post_type( $post_id );

			if ( ! $post_type ) {
				return;
			}

			$fields = jet_engine()->meta_boxes->get_meta_fields_for_object( $post_type );

			// Woo Product Data meta boxes are stored in a separate context and grouped by box name.
			if ( 'product' === $post_type ) {
				$wc_groups = jet_engine()->meta_boxes->get_fields_for_context( 'woocommerce_product_data' );

				if ( ! empty( $wc_groups ) && is_array( $wc_groups ) ) {
					foreach ( $wc_groups as $wc_fields ) {
						if ( ! empty( $wc_fields ) && is_array( $wc_fields ) ) {
							$fields = array_merge( $fields, $wc_fields );
						}
					}
				}
			}

			if ( empty( $fields ) ) {
				return;
			}

			$to_sync = array();
			$skip    = array();

			foreach ( $fields as $field ) {
				if ( empty( $field['object_type'] ) || 'field' !== $field['object_type'] ) {
					continue;
				}

				if ( empty( $field['type'] ) || 'taxonomy' !== $field['type'] ) {
					continue;
				}

				if ( empty( $field['save_terms'] ) || ! filter_var( $field['save_terms'], FILTER_VALIDATE_BOOLEAN ) ) {
					continue;
				}

				$field_name = ! empty( $field['name'] ) ? $field['name'] : '';
				$taxonomy   = ! empty( $field['taxonomy'] ) ? sanitize_key( $field['taxonomy'] ) : '';

				if ( ! $field_name || ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}

				$has_field_in_request = array_key_exists( $field_name, $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

				if ( ! $has_field_in_request ) {
					// For <select multiple>, browser does not submit a key when nothing is selected.
					// If meta is already empty after product/meta save, treat it as an explicit clear.
					if ( $this->should_treat_missing_as_empty_selection( $post_id, $field_name, $field ) ) {
						$raw_value = array();
					} else {
						$skip[ $taxonomy ] = true;
						continue;
					}
				} else {
					$raw_value = wp_unslash( $_POST[ $field_name ] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				}

				$return_value = ! empty( $field['return_value'] ) ? sanitize_key( $field['return_value'] ) : 'term_id';
				$term_ids     = $this->normalize_submitted_values( $raw_value, $return_value, $taxonomy );

				if ( ! isset( $to_sync[ $taxonomy ] ) ) {
					$to_sync[ $taxonomy ] = array();
				}

				$to_sync[ $taxonomy ] = array_values(
					array_unique(
						array_merge( $to_sync[ $taxonomy ], $term_ids )
					)
				);
			}

			if ( empty( $to_sync ) ) {
				return;
			}

			foreach ( $to_sync as $taxonomy => $term_ids ) {
				if ( ! empty( $skip[ $taxonomy ] ) ) {
					continue;
				}

				if ( ! $this->can_assign_terms( $taxonomy ) ) {
					continue;
				}

				wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
			}

		}

		/**
		 * Check assign term capability.
		 *
		 * @param string $taxonomy Taxonomy slug.
		 *
		 * @return bool
		 */
		public function can_assign_terms( $taxonomy ) {

			$taxonomy_obj = get_taxonomy( $taxonomy );

			if ( ! $taxonomy_obj ) {
				return false;
			}

			$cap = ! empty( $taxonomy_obj->cap->assign_terms ) ? $taxonomy_obj->cap->assign_terms : '';

			if ( ! $cap ) {
				return true;
			}

			return current_user_can( $cap );

		}

		/**
		 * Normalize submitted values and resolve them to term IDs.
		 *
		 * @param mixed  $value        Submitted value.
		 * @param string $return_value Return value mode.
		 * @param string $taxonomy     Taxonomy slug.
		 *
		 * @return array
		 */
		public function normalize_submitted_values( $value, $return_value, $taxonomy ) {

			$values = Jet_Engine_Meta_Boxes_Taxonomy_Values_Helper::normalize_request_values( $value );
			$result = array();

			foreach ( $values as $raw_value ) {
				if ( '' === $raw_value || null === $raw_value ) {
					continue;
				}

				$term_id = 0;

				switch ( $return_value ) {
					case 'term_slug':
						$term = get_term_by( 'slug', sanitize_title( $raw_value ), $taxonomy );
						$term_id = $term ? absint( $term->term_id ) : 0;
						break;

					case 'term_name':
						$term = get_term_by( 'name', sanitize_text_field( $raw_value ), $taxonomy );
						$term_id = $term ? absint( $term->term_id ) : 0;
						break;

					case 'term_id':
					default:
						$term_id = absint( $raw_value );
						break;
				}

				if ( ! $term_id ) {
					continue;
				}

				$term = get_term( $term_id, $taxonomy );

				if ( ! $term || is_wp_error( $term ) ) {
					continue;
				}

				$result[] = $term_id;
			}

			return array_values( array_unique( $result ) );

		}

		/**
		 * Decide if missing request key should be treated as explicit empty selection.
		 *
		 * @param int    $post_id    Current post ID.
		 * @param string $field_name Field name.
		 * @param array  $field      Field settings.
		 *
		 * @return bool
		 */
		public function should_treat_missing_as_empty_selection( $post_id, $field_name, $field ) {

			$appearance = ! empty( $field['appearance'] ) ? sanitize_key( $field['appearance'] ) : 'select';

			if ( 'select' !== $appearance ) {
				return false;
			}

			$is_multiple = false;

			if ( isset( $field['multiple'] ) ) {
				$is_multiple = filter_var( $field['multiple'], FILTER_VALIDATE_BOOLEAN );
			} elseif ( isset( $field['is_multiple'] ) ) {
				$is_multiple = filter_var( $field['is_multiple'], FILTER_VALIDATE_BOOLEAN );
			}

			if ( ! $is_multiple ) {
				return false;
			}

			$stored_value = get_post_meta( $post_id, $field_name, true );

			return ( '' === $stored_value || null === $stored_value || array() === $stored_value || false === $stored_value );

		}

	}
}
