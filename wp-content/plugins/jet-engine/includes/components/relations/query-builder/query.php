<?php
namespace Jet_Engine\Relations\Query_Builder;

class Relations_Query extends \Jet_Engine\Query_Builder\Queries\Base_Query {

	use \Jet_Engine\Relations\Traits\Related_Items_By_Args;
	use \Jet_Engine\Query_Builder\Queries\Traits\Meta_Query_Trait;
	use \Jet_Engine\Query_Builder\Queries\Traits\Tax_Query_Trait;

	protected $current_query = null;
	protected $final_relation_query = null;
	protected $filtered_query = array();

	public function get_items() {
		$this->final_relation_query['filtered_query'] = $this->filtered_query;
		return parent::get_items();
	}

	/**
	 * Returns queries items
	 *
	 * @return array|object
	 */
	public function _get_items() {

		$query = $this->get_current_query();

		if ( ! $query
			|| ! is_object( $query )
			|| ! is_callable( array( $query, 'get_items' ) )
		) {
			return [];
		}

		return $query->get_items();
	}

	public function get_query_hash_args() {
		return $this->final_relation_query;
	}

	public function query_was_changed() {

		$stored_object_id = isset( $this->final_relation_query['rel_object_id'] )
		                    ? $this->final_relation_query['rel_object_id']
							: null;

		$actual_object_id  = $this->get_object_id( $this->final_query );

		if ( $actual_object_id !== $stored_object_id ) {
			return true;
		}

		return false;
	}

	public function after_query_setup() {
		if ( ! isset( $this->final_relation_query ) ) {
			$relation = $this->get_relation( $this->final_query );
			$args     = $relation->get_args();

			$this->final_relation_query = array(
				'parent_object'  => $args['parent_object'],
				'child_object'   => $args['child_object'],
				'parent_rel'     => $args['parent_rel'],
				'type'           => $args['type'],
				'db_table'       => $args['db_table'],
				'rel_object_id'  => $this->get_object_id( $this->final_query ),
			);
		}

		if ( ! isset( $this->final_relation_query['rel_object_id'] ) ) {
			$this->final_relation_query['rel_object_id'] = $this->get_object_id( $this->final_query );
		}
	}

	/**
	 * Get current query object
	 *
	 * @return \Jet_Engine\Relations\Types\Type_Query|null
	 */
	public function get_current_query() {

		if ( null === $this->current_query ) {

			if ( null === $this->final_query ) {
				$this->setup_query();
			}

			$related_items = $this->get_related_items( $this->final_query );
			$relation      = $this->get_relation( $this->final_query );

			$rel_object = isset( $this->final_query['rel_object'] ) ? $this->final_query['rel_object'] : 'child_object';

			$queried_type = $relation ? $relation->get_object_type_for( $rel_object ) : false;
			$queried_object_name = $relation ? $relation->get_object_name_for( $rel_object ) : false;

			// abort if some data is missing
			if (
				! $queried_type
				|| ! $queried_object_name
				|| ! $relation
			) {
				if ( ! class_exists( '\Jet_Engine\Relations\Types\Type_Query' ) ) {
					require_once jet_engine()->relations->component_path( 'types/type-query.php' );
				}
				return new \Jet_Engine\Relations\Types\Type_Query();
			}

			$this->current_query = $queried_type->query(
				array_merge(
					$this->final_query, array( 'related_items_ids' => $related_items )
				),
				$queried_object_name,
				$relation
			);

			$final_query = $this->current_query->get_final_query();

			if ( ! empty( $final_query ) ) {
				$this->final_query = array_merge( $this->final_query, $final_query );
			}
		}

		return $this->current_query;
	}

	public function get_object_id( $args ) {
		$object_id = false;
		
		$rel_object_from = isset( $args['rel_object_from'] ) ? $args['rel_object_from'] : 'current_object';
		$rel_object_var  = isset( $args['rel_object_var'] ) ? $args['rel_object_var'] : '';

		if ( $rel_object_from ) {

			$object_id = jet_engine()->relations->sources->get_id_by_source( $rel_object_from, $rel_object_var );

			if ( ! $object_id ) {
				return false;
			}

		}

		return $object_id;
	}

	/**
	 * Returns query type for 3rd party integrations.
	 * For any internal usage take property directly
	 *
	 * @return string
	 */
	public function get_query_type() {

		$current_query = $this->get_current_query();

		$final_query = $current_query ? $current_query->get_final_query() : array();

		if ( ! empty( $final_query['_query_type'] ) ) {
			return $final_query['_query_type'];
		}

		return $this->query_type;
	}

	/**
	 * Returns total found items count
	 *
	 * @return mixed
	 */
	public function get_items_total_count() {

		$cached = $this->get_cached_data( 'count' );

		if ( $cached ) {
			$result = $cached;
		} else {
			$query = $this->get_current_query();
			$result = $query->get_total_count();
			$this->update_query_cache( $result, 'count' );
		}

		return $result;
	}

	/**
	 * Return current listing grid page
	 *
	 * @return false|float|int
	 */
	public function get_current_items_page() {

		$page = ! empty( $this->final_query['paged'] ) ? $this->final_query['paged'] : false;

		if ( ! $page && ! empty( $this->final_query['page'] ) ) {
			$page = $this->final_query['page'];
		}

		if ( ! $page ) {
			$page = 1;
		}

		return $page;
	}

	/**
	 * Returns count of the items visible per single listing grid loop/page
	 *
	 * @return int
	 */
	public function get_items_per_page() {
		$this->setup_query();
		return ! empty( $this->final_query['max_items'] ) ? absint( $this->final_query['max_items'] ) : 0;
	}

	/**
	 * Returns queried items count per page
	 *
	 * @return mixed
	 */
	public function get_items_page_count() {

		$result   = $this->get_items_total_count();
		$per_page = $this->get_items_per_page();

		if ( ! $per_page ) {
			return $result;
		}

		if ( $per_page < $result ) {
			$page  = $this->get_current_items_page();
			$pages = $this->get_items_pages_count();

			if ( $page < $pages ) {
				$result = $per_page;
			} elseif ( $page == $pages ) {
				$offset = ! empty( $this->final_query['offset'] ) ? absint( $this->final_query['offset'] ) : 0;

				if ( $result % $per_page > 0 ) {
					$result = ( $result % $per_page ) - $offset;
				} else {
					$result = $per_page - $offset;
				}
			}
		}

		return $result;
	}

	/**
	 * Returns queried items pages count
	 *
	 * @return false|float|int
	 */
	public function get_items_pages_count() {

		$per_page = $this->get_items_per_page();
		$total    = $this->get_items_total_count();

		if ( ! $per_page || ! $total ) {
			return 1;
		} else {
			return ceil( $total / $per_page );
		}

	}

	/**
	 * Set filtered prop in specific for current query type way
	 *
	 * @param string $prop
	 * @param null   $value
	 */
	public function set_filtered_prop( $prop = '', $value = null ) {
		$this->filtered_query[ $prop ] = $value;

		switch ( $prop ) {
			case '_page':
				$this->final_query['page'] = $value;
				break;

			case 'post__in':

				if ( ! empty( $this->final_query['include'] ) ) {
					$this->final_query['include'] = array_intersect( $this->final_query['include'], $value );

					if ( empty( $this->final_query['include'] ) ) {
						$this->final_query['include'] = array( PHP_INT_MAX );
					}

				} else {
					$this->final_query['include'] = $value;
				}

				break;

			case 'post__not_in':

				if ( ! empty( $this->final_query['exclude'] ) ) {
					$this->final_query['exclude'] = array_merge( $this->final_query['exclude'], $value );
				} else {
					$this->final_query['exclude'] = $value;
				}

				break;

			case 'orderby':
			case 'order':
			case 'meta_key':
				$this->set_filtered_order( $prop, $value );
				break;

			case 'meta_query':
				$this->replace_meta_query_row( $value );
				break;

			case 'tax_query':
				$this->replace_tax_query_row( $value );
				break;

			default:
				$this->final_query[ $prop ] = $value;
				break;
		}
	}

	/**
	 * Set filtering order for current query type way
	 *
	 * @param $key
	 * @param $value
	 */
	public function set_filtered_order( $key, $value ) {

		if ( empty( $this->final_query['orderby'] ) ) {
			$this->final_query['orderby'] = 'ID';
		}

		$this->final_query[ $key ] = $value;

	}

	/**
	 * Array of arguments where string should be exploded into array
	 *
	 * @return string[]
	 */
	public function get_args_to_explode() {
		return [
			'include',
			'exclude',
		];
	}

	/**
	 * Reset Query.
	 *
	 * @return void
	 */
	public function reset_query() {
		$this->current_query = null;
		unset( $this->final_relation_query['rel_object_id'] );
	}
}
