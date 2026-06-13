<?php
/**
 * Shared helpers for taxonomy field submitted values normalization.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Meta_Boxes_Taxonomy_Values_Helper' ) ) {

	class Jet_Engine_Meta_Boxes_Taxonomy_Values_Helper {

		/**
		 * Normalize request values into a flat array.
		 *
		 * @param mixed $value Raw value.
		 *
		 * @return array
		 */
		public static function normalize_request_values( $value ) {

			if ( is_array( $value ) ) {
				$normalized = array();

				foreach ( $value as $key => $item ) {
					if ( is_array( $item ) ) {
						$normalized = array_merge( $normalized, self::normalize_request_values( $item ) );
						continue;
					}

					// Checkbox control submits associative map: option_key => "true"/"false".
					// Option keys can be numeric (for term_id mode), so rely on value shape, not key type.
					if ( self::is_checkbox_flag_value( $item ) ) {
						$is_selected = filter_var( $item, FILTER_VALIDATE_BOOLEAN );

						if ( $is_selected ) {
							$normalized[] = $key;
						}

						continue;
					}

					$normalized[] = $item;
				}

				return $normalized;
			}

			if ( is_string( $value ) && false !== strpos( $value, ',' ) ) {
				$value = array_map( 'trim', explode( ',', $value ) );
				return array_filter( $value, function( $item ) {
					return '' !== $item;
				} );
			}

			return array( $value );

		}

		/**
		 * Check if a submitted scalar value is a checkbox flag from CX checkbox control.
		 *
		 * @param mixed $value Submitted scalar value.
		 *
		 * @return bool
		 */
		public static function is_checkbox_flag_value( $value ) {

			if ( is_bool( $value ) ) {
				return true;
			}

			if ( ! is_string( $value ) ) {
				return false;
			}

			$value = strtolower( trim( $value ) );

			return in_array( $value, array( 'true', 'false' ), true );

		}
	}
}

