<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Relations;

use Jet_Engine\Modules\Custom_Content_Types\Module;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Type extends \Jet_Engine\Relations\Types\Base {

	/**
	 * Returns type name
	 * @return [type] [description]
	 */
	public function get_name() {
		return Manager::instance()->slug();
	}

	/**
	 * Returns query type for current relation type.
	 * Used for match relations query type with appropriate query builder query type.
	 *
	 * @return string
	 */
	public function get_query_type() {
		return \Jet_Engine\Modules\Custom_Content_Types\Query_Builder\Manager::instance()->slug;
	}

	/**
	 * Returns type label
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Custom Content Types', 'jet-engine' );
	}

	/**
	 * Returns subtypes list
	 * @return [type] [description]
	 */
	public function get_object_names() {

		$items  = Module::instance()->manager->data->get_items();
		$result = array();

		foreach ( $items as $item ) {

			$args = maybe_unserialize( $item['args'] );

			$result[ $args['slug'] ] = array(
				'value'        => $args['slug'],
				'label'        => $args['name'],
				'label_single' => $args['name'],
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

		$content_type = Module::instance()->manager->get_content_types( $object_name );

		if ( ! $content_type ) {
			return false;
		}

		return $content_type->user_has_access();
	}

	/**
	 * Returns type items
	 * @return [type] [description]
	 */
	public function get_items( $object_name, $relation ) {

		$content_type = Module::instance()->manager->get_content_types( $object_name );
		$full_name    = jet_engine()->relations->types_helper->type_name_by_parts( 'cct', $object_name );

		if ( ! $content_type ) {
			return array();
		}

		$args        = $relation->get_args( 'cct', array() );
		$title_field = ! empty( $args[ $full_name ]['title_field'] ) ? $args[ $full_name ]['title_field'] : false;
		$items       = $content_type->db->query();

		return array_values( array_map( function( $item ) use ( $title_field ) {

			$title = '#' . $item['_ID'];

			if ( $title_field && isset( $item[ $title_field ] ) ) {
				$title = $item[ $title_field ] . ' (' . $title . ')';
			}

			return array(
				'value' => $item['_ID'],
				'label' => $title,
			);

		}, $items ) );

	}

	/**
	 * Sanitize CCT-related relation edit arguments
	 *
	 * @param  array  $args    [description]
	 * @param  array  $request [description]
	 * @return [type]          [description]
	 */
	public function sanitize_relation_edit_args( $args = array(), $request = array() ) {

		$args['cct'] = array();

		$cct_args = isset( $request['cct'] ) ? $request['cct'] : array();

		if ( ! is_array( $cct_args ) ) {
			$cct_args = array();
		}

		$args['cct'] = $cct_args;

		return $args;

	}

	/**
	 * Returns type items
	 */
	public function get_type_item_title( $item_id, $object_name, $relation ) {

		$content_type = Module::instance()->manager->get_content_types( $object_name );
		$title        = '#' . $item_id;

		if ( ! $content_type ) {
			return $title;
		}

		$args        = $relation->get_args( 'cct', array() );
		$full_name   = jet_engine()->relations->types_helper->type_name_by_parts( 'cct', $object_name );
		$title_field = ! empty( $args[ $full_name ]['title_field'] ) ? $args[ $full_name ]['title_field'] : false;

		$flag = \ARRAY_A;
		$content_type->db->set_format_flag( $flag );

		$item = $content_type->db->get_item( $item_id );

		if ( $item && $title_field && isset( $item[ $title_field ] ) ) {

			$title_format = '%1$s (%2$s)';
			$title_format = apply_filters( 'jet-engine/custom-content-types/relations/item-title/format', $title_format );

			$title = sprintf( $title_format, $item[ $title_field ], $title );
		}

		return $title;
	}

	/**
	 * Returns item edit URL by object type data and item ID
	 */
	public function get_type_item_edit_url( $item_id, $object_name, $relation ) {

		$content_type = Module::instance()->manager->get_content_types( $object_name );

		if ( ! $content_type ) {
			return false;
		}

		return $content_type->admin_pages->page_url( 'edit', $item_id );
	}

	/**
	 * Returns item view URL by object type data and item ID
	 *
	 * @param  [type] $type    [description]
	 * @param  [type] $item_id [description]
	 * @return [type]          [description]
	 */
	public function get_type_item_view_url( $item_id, $object_name, $relation ) {
		return false;
	}

	/**
	 * Trash given post
	 *
	 * @return [type] [description]
	 */
	public function delete_item( $item_id, $object_name ) {

		$content_type = Module::instance()->manager->get_content_types( $object_name );

		if ( ! $content_type ) {
			return false;
		}

		// todo add separate delete method to CCT handler and use it
		return $content_type->db->delete( array( '_ID' => $item_id ) );

	}

	/**
	 * Returns fields list required to create item of given type
	 *
	 * @param  [type] $object_name [description]
	 * @return [type]       [description]
	 */
	public function get_create_control_fields( $object_name, $relation ) {

		$content_type = Module::instance()->manager->get_content_types( $object_name );

		if ( ! $content_type ) {
			return array();
		}

		$args          = $relation->get_args( 'cct', array() );
		$full_name     = jet_engine()->relations->types_helper->type_name_by_parts( 'cct', $object_name );
		$create_fields = ! empty( $args[ $full_name ]['create_fields'] ) ? $args[ $full_name ]['create_fields'] : array();
		$result        = array();

		if ( empty( $create_fields ) ) {
			return array();
		}

		$cct_edit_page = $content_type->admin_pages->get_edit_page_instance( array(), false );
		$cct_edit_page->set_blocks_flag();
		$cct_edit_page->setup_page_fields();
		$all_fields = $cct_edit_page->get_prepared_fields();

		foreach ( $create_fields as $field_name ) {

			$field_data = isset( $all_fields[ $field_name ] ) ? $all_fields[ $field_name ] : false;

			$result[] = $field_data;

		}

		return $result;

	}

	/**
	 * Create new item of given typer by given data
	 *
	 * @return [type] [description]
	 */
	public function create_item( $data, $object_name ) {

		$content_type = Module::instance()->manager->get_content_types( $object_name );

		if ( ! $content_type ) {
			return false;
		}

		$handler = $content_type->get_item_handler();
		$item_id = $handler->update_item( $data );

		return $item_id;

	}

	/**
	 * Returns object of current type by item ID of this object
	 *
	 * @return [type] [description]
	 */
	public function get_object_by_id( $item_id, $object_name ) {

		$content_type = Module::instance()->manager->get_content_types( $object_name );

		if ( ! $content_type ) {
			return false;
		}

		return $content_type->db->get_item( $item_id );
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

		return ( isset( $object->cct_slug ) && $object->cct_slug === $object_name ) ? true : false;
	}

	/**
	 * Query posts of given post type by provided arguments.
	 *
	 * @param array                            $args        Query arguments.
	 * @param string                           $object_name Post type name.
	 * @param \Jet_Engine\Relations\Relation $relation    Relation instance.
	 *
	 * @return object
	 */
	public function query( $args, $object_name, $relation ) {

		$this->ensure_type_query_classs();

		$ids = isset( $args['related_items_ids'] ) ? $args['related_items_ids'] : array();

		if ( empty( $ids ) ) {
			return new \Jet_Engine\Relations\Types\Type_Query();
		}

		$content_type = Module::instance()->manager->get_content_types( $object_name );

		if ( ! $content_type ) {
			return new \Jet_Engine\Relations\Types\Type_Query();
		}

		unset( $args['related_items_ids'] );

		$query = new \Jet_Engine\Modules\Custom_Content_Types\Query_Builder\CCT_Query( [
			'type' => \Jet_Engine\Modules\Custom_Content_Types\Query_Builder\Manager::instance()->slug,
			'query' => [
				'order' => [ [
					'orderby' => 'preserve_ids',
					'order'   => 'ASC',
				] ]
			],
			'dynamic_query' => []
		] );

		$query->setup_query();

		$query->set_filtered_prop( 'content_type', $object_name );

		$query->set_filtered_prop(
			'meta_query',
			[ [
				'key' => '_ID',
				'compare' => 'IN',
				'value' => $ids,
				'type' => 'NUMERIC',
			] ]
		);

		if ( ! empty( $args['max_items'] ) ) {
			$query->set_filtered_prop( 'number', absint( $args['max_items'] ) );
			unset( $args['max_items'] );
		}

		$skip_args = array(
			'content_type',
			'max_items',
			'items_to_get',
			'relation',
			'rel_id',
			'rel_object',
			'rel_object_from',
			'_query_type',
			'queried_object_id',
			'jet_smart_filters',
			'suppress_filters',
		);

		foreach ( $args as $arg_key => $arg_value ) {

			if ( in_array( $arg_key, $skip_args, true ) ) {
				continue;
			}

			$query->set_filtered_prop( $arg_key, $arg_value );
		}

		$items = $query->_get_items(); // Call internal function to avoid overlaps with some query hooks

		return apply_filters(
			'jet-engine/relations/types/posts/query',
			new \Jet_Engine\Relations\Types\Type_Query(
				$items, // Call internal function to avoid overlaps with some query hooks
				count( $items ),
				$query->final_query
			),
			$query,
			$object_name,
			$relation
		);
	}

	/**
	 * Return JetSmartFilters-prepared query arguments array of given ids for given object type
	 *
	 * @return array()
	 */
	public function filtered_query_args( $ids = array(), $object_name = '' ) {
		return array(
			'meta_query' => array( array(
				'key'     => '_ID',
				'compare' => 'IN',
				'value'   => $ids,
			) ),
		);
	}

	public function merge_filtered_query_args( $args = array(), $new_args = array(), $object_name = ''  ) {
		$args = array_merge_recursive( $args, $new_args );
		$ids  = array();

		foreach ( $args['meta_query'] ?? array() as $i => $arg ) {
			if ( empty( $arg['key'] ) || empty( $arg['compare'] ) ) {
				continue;
			}

			if ( $arg['key'] === '_ID' && $arg['compare'] === 'IN' && is_array( $arg['value'] ?? '' ) ) {
				$ids[] = $arg['value'];
				unset( $args['meta_query'][ $i ] );
			}
		}

		if ( empty( $ids ) ) {
			return $args;
		}

		$args['meta_query'][] = array(
			'key'     => '_ID',
			'compare' => 'IN',
			'value'   => count( $ids ) < 2 ? $ids[0] : array_intersect( ...$ids ),
		);

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

		add_action( 'jet-engine/custom-content-types/delete-item/' . $object_name, function( $item_id, $item ) use ( $callback, $type_name ) {
			call_user_func( $callback, $type_name, $item_id );
		}, 10, 2 );
	}
}
