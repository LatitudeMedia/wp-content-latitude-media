<?php
namespace Jet_Engine\Modules\Data_Stores\Query_Builder;

use Jet_Engine\Modules\Data_Stores\Module;
use WP_Query;
use WP_User_Query;

class Data_Stores_Query extends \Jet_Engine\Query_Builder\Queries\Base_Query {

	use \Jet_Engine\Query_Builder\Queries\Traits\Meta_Query_Trait;
	use \Jet_Engine\Query_Builder\Queries\Traits\Tax_Query_Trait;

	protected $current_query = null;
	protected $current_store = null;

	/**
	 * Returns queries items
	 *
	 * @return array|object
	 */
	public function _get_items() {

		$query = $this->get_current_query();

		if ( is_object( $query ) ) {
			return $query->get_items();
		} else {
			return [];
		}
	}

	public function get_current_query() {

		if ( null === $this->current_query ) {

			if ( null === $this->final_query ) {
				$this->setup_query();
			}

			$store_slug = ! empty( $this->final_query['store_slug'] ) ? $this->final_query['store_slug'] : '';

			$store = Module::instance()->stores->get_store( $store_slug );

			if ( ! $store ) {
				return null;
			}

			$front_store_posts = ! empty( $this->final_query['post__in'] ) ? $this->final_query['post__in'] : [];

			if (
				! empty( $front_store_posts )
				&& is_string( $front_store_posts )
				&& false !== strpos( $front_store_posts, 'is-front' )
			) {
				// Prevent from processing queries where we already set front store trigger
				return null;
			}

			if ( empty( $front_store_posts )
				&& $store->get_type()->is_front_store() ) {

				$front_query_args = array(
					'is-front',
					$store->get_type()->type_id(),
					$store->get_slug(),
				);

				$this->final_query['post__in'] = implode( ',', $front_query_args );

				return null;
			}

			if ( ! empty( $front_store_posts ) ) {
				$this->current_store = is_array( $front_store_posts ) ? $front_store_posts : explode( ',', $front_store_posts );
			} else {
				$this->current_store = $store->get_store();
			}

			if ( empty( $this->current_store ) || ! is_array( $this->current_store ) ) {
				return null;
			}

			$max_items = isset( $this->final_query['max_items'] ) ? absint( $this->final_query['max_items'] ) : -1;

			$query = apply_filters(
				'jet-engine/data-stores/query-builder/store-pre-query',
				null,
				$store,
				$this->current_store,
				$this->final_query
			);

			$filtered_query = $this->final_query;

			// Unset known query_args which are 100% not related to filters
			$unset_args = array(
				'store_slug',
				'max_items',
				'_query_type',
				'queried_object_id',
				'jet_smart_filters',
				'suppress_filters',
			);

			foreach ( $unset_args as $arg ) {
				if ( isset( $filtered_query[ $arg ] ) ) {
					unset( $filtered_query[ $arg ] );
				}
			}

			foreach ( $filtered_query as $key => $value ) {
				$this->set_filtered_prop( $key, $value );
			}

			if ( null === $query ) {

				if ( $store->is_user_store() ) {

					$args = [
						'include'     => $this->current_store,
						'orderby'     => 'include',
						'order'       => 'ASC',
						'_query_type' => 'users',
					];

					if ( $max_items > 0 ) {
						$args['number'] = $max_items;
					} else {
						$args['number'] = -1;
					}

					if ( ! empty( $filtered_query ) ) {

						if ( ! empty( $filtered_query['post__in'] ) ) {
							$filtered_query['include'] = $filtered_query['post__in'];
							unset( $filtered_query['post__in'] );
						}

						$args = array_merge( $args, $filtered_query );
					}

					if ( ! empty( $args['paged'] ) ) {
						$paged          = absint( $args['paged'] );
						$args['number'] = $max_items;
						$args['offset'] = ( $paged - 1 ) * $max_items;
						unset( $args['paged'] );
					}

					$query = new WP_User_Query( $args );

					$this->current_query = new Query_Result(
						$query->get_results(),
						(int) $query->get_total(),
						$args
					);
				} else {

					$args = [
						'post_type'           => 'any',
						'posts_per_page'      => $max_items,
						'post__in'            => $this->current_store,
						'orderby'             => 'post__in',
						'ignore_sticky_posts' => true,
						'_query_type'         => 'posts',
					];

					if ( ! empty( $filtered_query ) ) {
						$args = array_merge( $args, $filtered_query );
					}

					$query = new WP_Query( $args );

					$this->current_query = new Query_Result(
						$query->posts,
						(int) $query->found_posts,
						$args
					);
				}
			} elseif ( is_object( $query ) ) {
				$this->current_query = $query;
			}

			if ( $this->current_query ) {
				$final_query = $this->current_query->get_final_query();

				if ( ! empty( $final_query ) && is_array( $final_query ) ) {
					$this->final_query = array_merge( $this->final_query, $final_query );
				}
			}
		}

		return $this->current_query;
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

			if ( $query && is_object( $query ) ) {
				$result = $query->get_total_count();
			} else {
				$result = 0;
			}

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
		switch ( $prop ) {
			case '_page':
				$this->final_query['page'] = $value;
				$this->final_query['paged'] = $value;
				break;

			case 'post__in':

				if (
					! empty( $this->final_query['post__in'] )
					&& is_array( $this->final_query['post__in'] )
					&& is_array( $value )
				) {
					$this->final_query['post__in'] = array_intersect( $this->final_query['post__in'], $value );

					if ( empty( $this->final_query['post__in'] ) ) {
						$this->final_query['post__in'] = array( PHP_INT_MAX );
					}

				} else {
					$this->final_query['post__in'] = $value;
				}

				break;

			case 'post__not_in':

				if ( ! empty( $this->final_query['post__not_in'] ) ) {
					$this->final_query['post__not_in'] = array_merge( $this->final_query['post__not_in'], $value );
				} else {
					$this->final_query['post__not_in'] = $value;
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
		$this->current_store = null;
		$this->current_query = null;
	}
}
