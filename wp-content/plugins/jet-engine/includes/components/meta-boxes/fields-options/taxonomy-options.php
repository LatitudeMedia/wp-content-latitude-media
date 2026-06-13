<?php
namespace Jet_Engine\Meta_Boxes\Option_Sources;

class Taxonomy_Options extends Manual_Bulk_Options {

	public $source_name = 'taxonomy';

	/**
	 * Parse taxonomy terms into options list.
	 *
	 * @param array $field Current field settings.
	 *
	 * @return array
	 */
	public function parse_options( $field ) {

		$taxonomy = ! empty( $field['taxonomy'] ) ? sanitize_key( $field['taxonomy'] ) : '';

		if ( ! $taxonomy || ! taxonomy_exists( $taxonomy ) ) {
			return array();
		}

		$return_value = ! empty( $field['return_value'] ) ? sanitize_key( $field['return_value'] ) : 'term_id';
		$args         = $this->prepare_terms_args( $field, $taxonomy );
		$terms        = get_terms( $args );
		$result       = array();

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return $result;
		}

		foreach ( $terms as $term ) {
			$result[] = array(
				'key'        => \Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper::get_term_value( $term, $return_value ),
				'value'      => \Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper::get_term_label( $term ),
				'is_checked' => false,
			);
		}

		return $result;

	}

	/**
	 * Prepare sanitized terms query args.
	 *
	 * @param array  $field    Current field settings.
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array
	 */
	public function prepare_terms_args( $field, $taxonomy ) {

		$args = \Jet_Engine_Meta_Boxes_Taxonomy_Terms_Helper::prepare_terms_args(
			$field,
			$taxonomy,
			array(
				'orderby' => array( 'terms_orderby', 'orderby' ),
				'order'   => array( 'terms_order', 'order' ),
			)
		);

		return apply_filters( 'jet-engine/meta-boxes/taxonomy-options/terms-args', $args, $field, $taxonomy );
	}

}
