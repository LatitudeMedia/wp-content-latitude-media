<?php
namespace Jet_Engine\Relations\Traits;

trait Related_Items_By_Args {

	/**
	 * Get relation object by args
	 *
	 * @param array $args
	 * @return false|\Jet_Engine\Relations\Relation
	 */
	public function get_relation( $args = [] ) {

		$rel_id = isset( $args['rel_id'] ) ? $args['rel_id'] : false;

		if ( ! $rel_id ) {
			return false;
		}

		$relation = jet_engine()->relations->get_active_relations( $rel_id );

		if ( ! $relation ) {
			return false;
		}

		return $relation;

	}

	/**
	 * Return related items IDs by given arguments.
	 * Allows to use the same logic to get related items accross different instances
	 * by using the same arguments structure.
	 *
	 * @param array $args
	 * @return array
	 */
	public function get_related_items( $args = [] ) {

		$rel_id          = isset( $args['rel_id'] ) ? $args['rel_id'] : false;
		$rel_object      = isset( $args['rel_object'] ) ? $args['rel_object'] : 'child_object';
		$rel_object_from = isset( $args['rel_object_from'] ) ? $args['rel_object_from'] : 'current_object';
		$rel_object_var  = isset( $args['rel_object_var'] ) ? $args['rel_object_var'] : '';

		if ( ! $rel_id ) {
			return;
		}

		$relation = $this->get_relation( $args );

		if ( ! $relation ) {
			return;
		}

		$object_id = false;

		if ( $rel_object_from ) {

			$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

			if ( ! $object_id ) {
				return false;
			}

		}

		$related_ids = array();

		switch ( $rel_object ) {
			case 'parent_object':
				$related_ids = $relation->get_parents( $object_id, 'ids' );
				break;

			default:
				$related_ids = $relation->get_children( $object_id, 'ids' );
				break;
		}

		return $related_ids;
	}
}