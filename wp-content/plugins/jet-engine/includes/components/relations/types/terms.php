<?php
namespace Jet_Engine\Relations\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Terms extends Base {

	private $filter_key = 'include';

	/**
	 * Returns type name
	 * @return [type] [description]
	 */
	public function get_name() {
		return 'terms';
	}

	/**
	 * Returns type label
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Taxonomy Terms', 'jet-engine' );
	}

	/**
	 * Returns subtypes list
	 * @return [type] [description]
	 */
	public function get_object_names() {

		$taxonomies = get_taxonomies( array(), 'objects' );
		$result     = array();

		foreach ( $taxonomies as $tax ) {
			$result[ $tax->name ] = array(
				'value'        => $tax->name,
				'label'        => $tax->label,
				'label_single' => $tax->labels->singular_name,
			);
		}

		return $result;
	}

	/**
	 * Checkk type specific user capabilities
	 *
	 * @return [type] [description]
	 */
	public function current_user_can( $cap, $item_id, $object_name ) {

		switch ( $cap ) {
			case 'edit':
			case 'delete':
				return current_user_can( $cap . '_term', $item_id );

			default:
				return true;
		}
	}

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	public function get_items( $object_name, $relation ) {

		$terms = get_terms( array(
			'taxonomy'   => $object_name,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'DESC',
			'fields'     => 'id=>name',
		) );

		$result = array();

		foreach ( $terms as $tid => $tname ) {
			$result[] = array(
				'value' => $tid,
				'label' => $tname,
			);
		}

		return $result;

	}

	/**
	 * Returns type items
	 */
	public function get_type_item_title( $item_id, $object_name, $relation ) {

		$term   = get_term( $item_id );
		$result = '#' . $item_id;

		if ( $term ) {
			$result = $term->name;
		}

		return $result;
	}

	/**
	 * Returns item edit URL by object type data and item ID
	 */
	public function get_type_item_edit_url( $item_id, $object_name, $relation ) {
		return get_edit_term_link( $item_id, $object_name );
	}

	/**
	 * Returns item view URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_view_url( $item_id, $object_name, $relation ) {
		return get_term_link( intval( $item_id ), $object_name );
	}

	/**
	 * Trash given post
	 *
	 * @return [type] [description]
	 */
	public function delete_item( $item_id, $object_name ) {

		if ( ! $this->current_user_can( 'delete', $item_id, $object_name ) ) {
			return false;
		}

		return wp_delete_term( $item_id, $object_name );

	}

	/**
	 * Returns fields list required to create item of given type
	 *
	 * @param  [type] $object_name [description]
	 * @return [type]       [description]
	 */
	public function get_create_control_fields( $object_name, $relation ) {

		return apply_filters( 'jet-engine/relations/types/terms/create-fields', array(
			array(
				'name'  => 'term_name',
				'title' => __( 'Term Name', 'jet-engine' ),
				'type'  => 'text',
			),
		), $object_name, $relation );

	}

	/**
	 * Create new item of given typer by given data
	 *
	 * @return [type] [description]
	 */
	public function create_item( $data, $object_name ) {

		if ( ! taxonomy_exists( $object_name ) ) {
			return false;
		}

		$term_name = ! empty( $data['term_name'] ) ? $data['term_name'] : '';
		$term      = wp_insert_term( $term_name, $object_name );

		if ( is_wp_error( $term ) ) {
			return false;
		}

				do_action( 'jet-engine/relations/types/terms/on-create', $term['term_id'], $data, $object_name, $term );

				return $term['term_id'];

		}

	/**
		* Query terms of given taxonomy by provided arguments.
		*
		* @param array                            $args        Query arguments.
		* @param string                           $object_name Taxonomy slug.
		* @param \Jet_Engine\Relations\Relation $relation    Relation instance.
		*
		* @return array
		*/
	public function query( $args, $object_name, $relation ) {

		$this->ensure_type_query_classs();

		if ( ! taxonomy_exists( $object_name ) ) {
			return new Type_Query();
		}

		$ids = isset( $args['related_items_ids'] ) ? $args['related_items_ids'] : array();

		// adjust the rest of the known arguments according WP_Term_Query format.
		if ( ! empty( $args['orderby'] ) && 'ID' === $args['orderby'] ) {
			$args['orderby'] = 'term_id';
		}

		if ( ! empty( $args['orderby'] ) && 'title' === $args['orderby'] ) {
			$args['orderby'] = 'name';
		}

		if ( ! empty( $args['max_items'] ) ) {

			$args['number'] = absint( $args['max_items'] );

			if ( ! empty( $args['page'] ) ) {
				$args['offset'] = ( absint( $args['page'] ) - 1 ) * $args['number'];
			} elseif ( ! empty( $args['offset'] ) ) {
				$args['offset'] = absint( $args['offset'] );
			}
		}

		if ( ! empty( $args['exclude'] ) && is_array( $args['exclude'] ) ) {
			$ids = array_diff( $ids, $args['exclude'] );
			unset( $args['exclude'] );
		}

		if ( ! empty( $args['include'] ) && is_array( $args['include'] ) ) {
			$ids = array_intersect( $ids, $args['include'] );
		}

		if ( empty( $ids ) ) {
			return new Type_Query();
		}

		$query_args = wp_parse_args(
			$args,
			array(
				'taxonomy'   => $object_name,
				'hide_empty' => false,
			)
		);

		if ( empty( $ids ) ) {
			return new Type_Query();
		}

		$query_args['include'] = $ids;
		unset( $query_args['related_items_ids'] );

		if ( empty( $query_args['orderby'] ) ) {
			$query_args['orderby'] = 'include';
		}

		$term_query = new \WP_Term_Query( $query_args );

		$query_args['_query_type'] = $this->get_query_type();

		if ( is_wp_error( $term_query ) ) {
			$terms = array();
		} else {
			$terms = $term_query->get_terms();
		}

		return apply_filters(
			'jet-engine/relations/types/terms/query',
			new Type_Query(
				$terms,
				count( $terms ),
				$query_args
			),
			$term_query,
			$object_name,
			$relation
		);
	}

	/**
	 * Returns object of current type by item ID of this object
	 *
	 * @return [type] [description]
	*/
	public function get_object_by_id( $item_id, $object_name ) {
		return get_term( $item_id, $object_name );
	}

	/**
	 * Check if $object is belongs to current type
	 *
	 * @param  [type]  $object      [description]
	 * @param  [type]  $object_name [description]
	 * @return boolean              [description]
	 */
	public function is_object_of_type( $object, $object_name ) {

		if ( ! $object || ! is_object( $object ) ) {
			return false;
		}

		$class = get_class( $object );

		if ( ! $class || 'WP_Term' !== $class ) {
			return false;
		}

		return ( $object_name === $object->taxonomy ) ? true : false;

	}

	public function filtered_arg( $object_name = '' ) {
		return 'include';
	}

	/**
	 * Register appropriate cleanup hook for current type items.
	 * This hook should be called on deletion of item of current type and call clean up method from relation
	 * See the default types for examples.
	 *
	 * @param  string $object_name [description]
	 * @param  [type] $callback    [description]
	 * @return [type]              [description]
	 */
	public function register_cleanup_hook( $object_name = '', $callback = null, $type_name = '' ) {

		add_action( 'delete_term', function( $term, $tt_id, $taxonomy ) use ( $object_name, $callback, $type_name ) {

			if ( $taxonomy && $taxonomy === $object_name ) {
				call_user_func( $callback, $type_name, $term );
			}

		}, 10, 3 );
	}

}
