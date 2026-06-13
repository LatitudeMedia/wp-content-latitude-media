<?php
/**
 * Shared helpers for taxonomy terms query args and value/label formatting.
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper' ) ) {

	class Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper {

		/**
		 * Build sanitized get_terms() args from field data.
		 *
		 * @param array  $field    Field settings.
		 * @param string $taxonomy Taxonomy slug.
		 * @param array  $map      Arg map: arg => [ field_key_1, field_key_2 ].
		 * @param array  $defaults Default query args.
		 *
		 * @return array
		 */
		public static function prepare_terms_args( $field, $taxonomy, $map = array(), $defaults = array() ) {

			$args = wp_parse_args(
				$defaults,
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);

			$terms_args = array();

			if ( ! empty( $field['terms_args'] ) && is_array( $field['terms_args'] ) ) {
				$terms_args = $field['terms_args'];
			}

			foreach ( $map as $arg_key => $field_keys ) {
				foreach ( $field_keys as $field_key ) {

					if ( isset( $terms_args[ $field_key ] ) ) {
						$value = $terms_args[ $field_key ];
					} elseif ( isset( $field[ $field_key ] ) ) {
						$value = $field[ $field_key ];
					} else {
						continue;
					}

					$args[ $arg_key ] = self::sanitize_arg_value( $arg_key, $value );
					break;
				}
			}

			return $args;

		}

		/**
		 * Sanitize get_terms() arg value.
		 *
		 * @param string $arg_key Arg key.
		 * @param mixed  $value   Arg value.
		 *
		 * @return mixed
		 */
		public static function sanitize_arg_value( $arg_key, $value ) {

			switch ( $arg_key ) {
				case 'order':
					$value = strtoupper( sanitize_text_field( $value ) );
					return in_array( $value, array( 'ASC', 'DESC' ), true ) ? $value : 'ASC';

				case 'orderby':
					$value = sanitize_key( $value );
					return in_array( $value, array( 'name', 'slug', 'term_id', 'count', 'include', 'description' ), true ) ? $value : 'name';

				default:
					return sanitize_text_field( $value );
			}

		}

		/**
		 * Convert term object into selected return value format.
		 *
		 * @param \WP_Term $term         Term object.
		 * @param string   $return_value Return format.
		 *
		 * @return int|string
		 */
		public static function get_term_value( $term, $return_value = 'term_id' ) {

			switch ( $return_value ) {
				case 'term_slug':
					return $term->slug;

				case 'term_name':
					return $term->name;

				case 'term_id':
				default:
					return (int) $term->term_id;
			}

		}

		/**
		 * Build hierarchical term label where needed.
		 *
		 * @param \WP_Term $term Term object.
		 *
		 * @return string
		 */
		public static function get_term_label( $term ) {

			if ( empty( $term->taxonomy ) || ! is_taxonomy_hierarchical( $term->taxonomy ) || empty( $term->parent ) ) {
				return $term->name;
			}

			$parents = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );

			if ( empty( $parents ) ) {
				return $term->name;
			}

			$parents = array_reverse( array_map( 'absint', $parents ) );
			$labels  = array();

			foreach ( $parents as $parent_id ) {
				$parent_term = get_term( $parent_id, $term->taxonomy );

				if ( ! $parent_term || is_wp_error( $parent_term ) ) {
					continue;
				}

				$labels[] = $parent_term->name;
			}

			$labels[] = $term->name;

			return implode( ' > ', $labels );

		}
	}
}

