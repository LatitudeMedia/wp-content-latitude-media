<?php
namespace Jet_Engine\Relations\Types;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base {

	/**
	 * Returns type name
	 * @return [type] [description]
	 */
	abstract public function get_name();

	/**
	 * Returns type label
	 * @return [type] [description]
	 */
	abstract public function get_label();

	/**
	 * Returns subtypes list
	 * @return [type] [description]
	 */
	abstract public function get_object_names();

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	abstract public function get_items( $object_name, $relation );

 /**
  * Retrieves the title of a specific type item.
  *
  * @param string                         $item_id     Item ID.
  * @param string                         $object_name Object name (post/user/etc.).
  * @param \Jet_Engine\Relations\Relation $relation    Relation instance.
  *
  * @return string The title of the specified item.
  */
 abstract public function get_type_item_title( $item_id, $object_name, $relation );

	/**
	 * Returns item edit URL by object type data and item ID
	 *
	 * @param string   $item_id     Item ID.
	 * @param string   $object_name Object name (post/user/etc.).
	 *
	 * @param \Jet_Engine\Relations\Relation $relation Relation instance.
	 *
	 * @return string Item edit link.
	 */
	abstract public function get_type_item_edit_url( $item_id, $object_name, $relation );

	/**
	 * Returns item view URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	abstract public function get_type_item_view_url( $item_id, $object_name, $relation );

	/**
	 * Returns query type for current relation type.
	 * Used for match relations query type with appropriate query builder query type.
	 *
	 * @return string
	 */
	public function get_query_type() {
		return $this->get_name();
	}

	/**
	 * Delete given item.
	 * By default not allowed, should be set for each type individually with appropriate capability check
	 *
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function delete_item( $item_id, $object_name ) {
		return false;
	}

	/**
	 * Checkk type specific user capabilities
	 *
	 * @return [type] [description]
	 */
	public function current_user_can( $cap, $item_id, $object_name ) {
		return true;
	}

	/**
	 * Returns fields list required to create item of given type
	 *
	 * @param  [type] $object_name [description]
	 * @return [type]       [description]
	 */
	public function get_create_control_fields( $object_name, $relation ) {
		return array();
	}

	/**
	 * Create new item of given typer by given data
	 *
	 * @return [type] [description]
	 */
	public function create_item( $data, $object_name ) {
		return false;
	}

	/**
	 * Check if $object is belongs to current type
	 *
	 * @param  [type]  $object      [description]
	 * @param  [type]  $object_name [description]
	 * @return boolean              [description]
	 */
	public function is_object_of_type( $object, $object_name ) {
		return false;
	}

	/**
	 * Returns object of current type by item ID of this object
	 *
	 * @return [type] [description]
	 */
	public function get_object_by_id( $item_id, $object_name ) {
		return false;
	}

	/**
	 * Sanitize type-specific arguments of relation on edit.
	 * Is placeholder method, by default returs input data without changes.
	 * Rewrite this method in the child class if you pass any additional controls into relation.
	 *
	 * @param  array  $final_args   [description]
	 * @param  array  $request_data [description]
	 * @return [type]               [description]
	 */
	public function sanitize_relation_edit_args( $final_args = array(), $request_data = array() ) {
		return $final_args;
	}

	public function filtered_arg( $object_name = '' ) {
		return '';
	}

	/**
	 * Ensure the \Jet_Engine\Relations\Types\Type_Query class is loaded.
	 *
	 * @return void
	 */
	public function ensure_type_query_classs() {
		if ( ! class_exists( '\Jet_Engine\Relations\Types\Type_Query' ) ) {
			require_once jet_engine()->relations->component_path( 'types/type-query.php' );
		}
	}

	/**
	 * Perform a query for the current type by given arguments.
	 *
	 * The arguments array should contain `related_items_ids` key which holds
	 * actual IDs of related items. Arguments should be formatted according
	 * to the current type logic.
	 *
	 * @param array                            $args        Query arguments.
	 * @param string                           $object_name Object name (post type/user/etc.).
	 * @param \Jet_Engine\Relations\Relation $relation    Relation instance.
	 *
	 * @return array
	 */
	abstract public function query( $args, $object_name, $relation );

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function filtered_query_args( $ids = array(), $object_name = '' ) {
		$arg = $this->filtered_arg( $object_name );

		if ( empty( $arg ) ) {
			return array();
		}

		return array( $arg => $ids );
	}

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function merge_filtered_query_args( $args = array(), $new_args = array(), $object_name = '' ) {
		$arg = $this->filtered_arg( $object_name );

		if ( empty( $arg ) ) {
			return $args;
		}

		if ( ! empty( $args[ $arg ] ) && ! empty( $new_args[ $arg ] ) ) {
			$args[ $arg ] = array_intersect( $args[ $arg ], $new_args[ $arg ] );
		} elseif ( ! empty( $new_args[ $arg ] ) ) {
			$args[ $arg ] = $new_args[ $arg ];
		}

		if ( empty( $args[ $arg ] ) ) {
			$args[ $arg ] = array( PHP_INT_MAX );
		}

		return $args;
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
		return false;
	}

}
