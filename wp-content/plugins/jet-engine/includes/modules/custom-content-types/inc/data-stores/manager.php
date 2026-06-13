<?php
namespace Jet_Engine\Modules\Custom_Content_Types\Data_Stores;

use Jet_Engine\Modules\Custom_Content_Types\Query_Builder\CCT_Query;
use Jet_Engine\Modules\Custom_Content_Types\Module;

class Manager {

	public $settings;

	public $script_enqueued = false;

	public function __construct() {

		require Module::instance()->module_path( 'data-stores/settings.php' );
		$this->settings = new Settings();

		add_filter(
			'jet-engine/data-stores/store-post-id',
			array( $this, 'set_type_id_as_post_id' ), 10, 2
		);

		add_action(
			'jet-engine/custom-content-types/elementor/after-query-control',
			array( $this, 'add_data_store_contols' ), 10, 2
		);

		add_filter(
			'jet-engine/custom-content-types/blocks/data',
			array( $this, 'add_blocks_data' )
		);

		add_filter(
			'jet-engine/blocks-views/listing-grid/attributes',
			array( $this, 'listing_grid_atts' )
		);

		add_action(
			'jet-engine/data-stores/post-count-increased',
			array( $this, 'update_item_count' ), 10, 3
		);

		add_action(
			'jet-engine/data-stores/post-count-decreased',
			array( $this, 'update_item_count' ), 10, 3
		);

		add_filter(
			'jet-engine/data-stores/pre-get-post-count',
			array( $this, 'get_item_count' ), 10, 3
		);

		add_filter(
			'jet-engine/custom-content-types/listing/query-args',
			array( $this, 'add_data_store_query' ), 10, 2
		);

		add_filter(
			'jet-engine/listing/container-atts',
			array( $this, 'add_store_data_attr' ), 10, 2
		);

		add_filter(
			'jet-engine/custom-content-types/item-to-update',
			array( $this, 'ensure_store_item_count_on_save' ), 10, 3
		);

		add_filter(
			'jet-engine/data-stores/query-builder/store-pre-query',
			array( $this, 'handle_data_store_query_type' ), 10, 4
		);

		// CCT front-store compatibility.
		// Fixes: Local Storage items not displayed for CCT
		// @see https://github.com/Crocoblock/issues-tracker/issues/18878
		add_filter(
			'jet-engine/data-stores/front-store/listing-query-args',
			array( $this, 'add_front_store_query_args' ), 10, 2
		);

		add_filter(
			'jet-engine/data-stores/front-store/setup-query',
			array( $this, 'setup_front_store_prop' ), 10, 3
		);
	}

	/**
	 * Process Data Store query type for Query Builder and CCT store
	 *
	 * @param mixed $result Query result.
	 * @param object $store Data Store instance.
	 * @param array|false $current_store Current store items.
	 * @param array $query_args Final query args.
	 */
	public function handle_data_store_query_type( $result, $store, $current_store, $query_args ) {

		if ( ! $store ) {
			return $result;
		}

		$is_cct      = $store->get_arg( 'is_cct' );
		$related_cct = $store->get_arg( 'related_cct' );

		if ( ! $is_cct || ! $related_cct ) {
			return $result;
		}

		$content_type = Module::instance()->manager->get_content_types( $related_cct );

		if ( ! $content_type ) {
			return $result;
		}

		$result = new \Jet_Engine\Modules\Data_Stores\Query_Builder\Query_Result( array(), 0, array() );

		if ( empty( $current_store ) ) {
			return $result;
		}

		$max_items = isset( $query_args['max_items'] ) ? absint( $query_args['max_items'] ) : 0;
		$cct_query_args = array(
			'content_type' => $related_cct,
			'status'       => 'publish',
			'order' => array( array(
				'orderby' => 'preserve_ids',
				'order'   => 'ASC',
			) ),
			'args' => array(
				array(
					'field'    => '_ID',
					'operator' => 'IN',
					'value'    => $current_store,
				),
			),
		);

		if ( $max_items > 0 ) {
			$cct_query_args['number'] = $max_items;
		}

		if ( ! empty( $query_args['paged'] ) ) {
			$paged          = absint( $query_args['paged'] );
			$cct_query_args['number'] = $max_items;
			$cct_query_args['offset'] = ( $paged - 1 ) * $max_items;
			unset( $query_args['paged'] );
		}

		$cct_query = new CCT_Query( array(
			'query' => $cct_query_args,
		) );

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
			if ( isset( $query_args[ $arg ] ) ) {
				unset( $query_args[ $arg ] );
			}
		}

		$cct_query->setup_query();

		foreach ( $query_args as $key => $value ) {
			$cct_query->set_filtered_prop( $key, $value );
		}

		$final_query = $cct_query->final_query;

		$final_query['_query_type'] = \Jet_Engine\Modules\Custom_Content_Types\Query_Builder\Manager::instance()->slug;

		return new \Jet_Engine\Modules\Data_Stores\Query_Builder\Query_Result(
			$cct_query->get_items(),
			(int) $cct_query->get_items_total_count(),
			$final_query
		);
	}

	public function add_blocks_data( $data ) {

		$all_stores = $this->settings->get_stores_for_type();
		$stores     = array(
			array(
				'value' => '',
				'label' => __( 'Not selected', 'jet-engine' )
			)
		);

		foreach ( $all_stores as $type => $type_stores ) {
			foreach ( $type_stores as $store ) {
				$stores[] = array(
					'value' => $store->get_slug(),
					'label' => $store->get_name(),
				);
			}
		}

		$data['stores'] = $stores;

		return $data;

	}

	public function listing_grid_atts( $attributes ) {

		$attributes['jet_cct_from_store'] = array(
			'type' => 'string',
			'default' => '',
		);

		return $attributes;

	}

	public function add_store_data_attr( $atts = array(), $settings = array() ) {

		if ( ! empty( $settings['jet_cct_from_store'] ) ) {

			$store          = $settings['jet_cct_from_store'];
			$data_stores    = jet_engine()->modules->get_module( 'data-stores' );
			$store_instance = $data_stores->instance->stores->get_store( $store );
			$is_cct         = $store_instance->get_arg( 'is_cct' );
			$related_cct    = $store_instance->get_arg( 'related_cct' );

			if ( $is_cct && $related_cct ) {

				$query  = isset( $settings['jet_cct_query'] ) ? $settings['jet_cct_query'] : '{}';
				$atts[] = 'data-is-store-listing="' . $store . '"';
				$atts[] = 'data-store-type="' . $store_instance->get_type()->type_id() . '"';
				$atts[] = 'data-cct-query="' . htmlspecialchars( $query ) . '"';

				$this->enqueue_store_trigger();

			}

		}

		return $atts;

	}

	public function enqueue_store_trigger() {

		if ( $this->script_enqueued ) {
			return;
		}

		add_action( 'jet-engine/listings/frontend-scripts', function() {

			ob_start();
			?>
			jQuery( document ).on( 'jet-listing-grid-init-store', function( event, $grid ) {

				$grid = jQuery( $grid );

				var storeSlug  = $grid.data( 'is-store-listing' ),
					storeType  = $grid.data( 'store-type' ),
					nav        = $grid.data( 'nav' ),
					query      = $grid.data( 'cct-query' ),
					store      = window.JetEngine.stores[ storeType ],
					posts      = [],
					$container = $grid.closest( '.jet-listing-grid' );

				if ( ! store ) {
					return;
				}

				posts = store.getStore( storeSlug );

				if ( ! posts.length ) {
					return;
				}

				query.args.push( {
					field: '_ID',
					operator: 'IN',
					value: posts,
				} );

				nav.widget_settings.jet_cct_query = JSON.stringify( query );

				JetEngine.ajaxGetListing( {
					handler: 'get_listing',
					container: $container,
					masonry: false,
					slider: false,
					append: false,
					query: query,
					widgetSettings: nav.widget_settings,
				}, function( response ) {
					JetEngine.widgetListingGrid( $container );
				} );

			} );
			<?php
			$data = ob_get_clean();
			$this->script_enqueued = wp_add_inline_script( 'jet-engine-frontend', $data );

		} );

	}

	public function get_item_count( $count = false, $item_id = false, $store = null ) {

		if ( ! $item_id ) {
			return $count;
		}

		$is_cct      = $store->get_arg( 'is_cct' );
		$related_cct = $store->get_arg( 'related_cct' );

		if ( ! $is_cct || ! $related_cct ) {
			return $count;
		}

		$content_type = Module::instance()->manager->get_content_types( $related_cct );

		if ( ! $content_type ) {
			return $count;
		}

		$item        = $content_type->db->get_item( $item_id );
		$count_field = $this->settings->get_count_field_name( $store );

		if ( ! $item ) {
			return 0;
		} else {
			if ( is_array( $item ) ) {
				$count = isset( $item[ $count_field ] ) ? $item[ $count_field ] : 0;
				return absint( $count );
			} else {
				return absint( $item->$count_field );
			}
		}

	}

	public function update_item_count( $item_id = 0, $new_count = 0, $store = null ) {

		if ( ! $item_id ) {
			return;
		}

		$is_cct      = $store->get_arg( 'is_cct' );
		$related_cct = $store->get_arg( 'related_cct' );

		if ( ! $is_cct || ! $related_cct ) {
			return;
		}

		$content_type = Module::instance()->manager->get_content_types( $related_cct );

		if ( ! $content_type ) {
			return;
		}

		$content_type->db->update(
			array( $this->settings->get_count_field_name( $store ) => $new_count ),
			array( '_ID' => $item_id )
		);

	}

	public function add_data_store_query( $query, $settings ) {

		if ( empty( $settings['jet_cct_from_store'] ) ) {
			return $query;
		}

		$store          = $settings['jet_cct_from_store'];
		$data_stores    = jet_engine()->modules->get_module( 'data-stores' );
		$store_instance = $data_stores->instance->stores->get_store( $store );

		if ( ! $store_instance ) {
			return $query;
		}

		$items = $store_instance->get_store();

		if ( empty( $items ) ) {
			return false;
		}

		$query[] = array(
			'field'    => '_ID',
			'operator' => 'IN',
			'value'    => $items,
		);

		return $query;

	}

	public function add_data_store_contols( $widget ) {

		$all_stores = $this->settings->get_stores_for_type();
		$stores     = array( '' => __( 'Not selected', 'jet-engine' ) );

		foreach ( $all_stores as $type => $type_stores ) {
			foreach ( $type_stores as $store ) {
				$stores[ $store->get_slug() ] = $store->get_name();
			}
		}

		$widget->add_control(
			'jet_cct_from_store',
			array(
				'label'       => __( 'Get items from store', 'jet-engine' ),
				'label_block' => true,
				'type'        => 'select',
				'default'     => '',
				'options'     => $stores,

			)
		);

	}

	public function set_type_id_as_post_id( $post_id, $store ) {

		$is_cct      = $store->get_arg( 'is_cct' );
		$related_cct = $store->get_arg( 'related_cct' );

		if ( ! $is_cct || ! $related_cct ) {
			return $post_id;
		}

		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $current_object ) {
			return $post_id;
		}

		$id_prop = $related_cct . '___ID';

		if ( isset( $current_object->$id_prop ) ) {
			return $current_object->$id_prop;
		} elseif ( isset( $current_object->cct_slug ) && $current_object->cct_slug === $related_cct ) {
			return $current_object->_ID;
		} else {
			return $post_id;
		}

	}

	public function ensure_store_item_count_on_save( $item, $fields, $item_handler ) {

		if ( empty( $item['_ID'] ) ) {
			return $item;
		}

		$prev_item = $item_handler->get_factory()->db->get_item( absint( $item['_ID'] ) );

		if ( empty( $prev_item ) ) {
			return $item;
		}

		$type = $item_handler->get_factory()->get_arg( 'slug' );

		if ( ! $type ) {
			return $item;
		}

		$data_stores = $this->settings->get_stores_for_type( $type );

		if ( ! empty( $data_stores ) ) {
			foreach ( $data_stores as $store ) {

				if ( ! $store->can_count_posts() ) {
					continue;
				}

				$count_name = $this->settings->get_count_field_name( $store );

				if ( ! empty( $prev_item[ $count_name ] ) ) {
					$item[ $count_name ] = $prev_item[ $count_name ];
				}
			}
		}

		return $item;
	}

	/**
	 * Adjust listing query arguments for front store listings.
	 *
	 * Handles AJAX load-more requests and allows external query types
	 * (e.g. CCT) to override front store behavior via filter.
	 * Falls back to default posts (CPT) handling if not overridden.
	 *
	 * @param array  $context  Listing query arguments.
	 * @param object $query    Query Builder query instance.
	 *
	 * @return array Modified query arguments.
	 */
	public function add_front_store_query_args( $context, $query ) {
		if ( ! isset( $query->query_type ) || 'custom-content-type' !== $query->query_type ) {
			return $context;
		}

		$final = ! empty( $query->final_query ) ? $query->final_query : array();

		if ( empty( $final['args'] ) || ! is_array( $final['args'] ) ) {
			return $context;
		}

		$store_data = null;

		foreach ( $final['args'] as $arg ) {
			if ( empty( $arg['value'] ) || ! is_string( $arg['value'] ) ) {
				continue;
			}
			if ( 0 === strpos( $arg['value'], 'is-front' ) ) {
				$store_data = explode( ',', $arg['value'] );
				break;
			}
		}

		if ( $store_data ) {
			$context['handled']          = true;
			$context['args']['post__in'] = $store_data;
		}

		return $context;
	}

	/**
	 * Handle front store ID injection for CCT queries.
	 *
	 * Replaces the front store marker value in CCT query arguments
	 * with actual stored item IDs during AJAX processing.
	 *
	 * @param bool   $handled Whether the front store logic was already handled.
	 * @param object $query   Query Builder query instance.
	 * @param array  $request AJAX request query payload ($_REQUEST['query']).
	 *
	 * @return bool True if handled for CCT, otherwise original $handled value.
	 */
	public function setup_front_store_prop( $handled, $query, $request ) {
		if ( ! isset( $query->query_type ) || 'custom-content-type' !== $query->query_type ) {
			return $handled;
		}

		$marker = '';

		if ( ! empty( $request['post__in'] ) && is_array( $request['post__in'] ) ) {
			$marker = implode( ',', array_values( $request['post__in'] ) );
		}

		$replaced = false;

		if ( $marker && ! empty( $query->final_query['args'] ) && is_array( $query->final_query['args'] ) ) {
			foreach ( $query->final_query['args'] as $i => $arg ) {
				if ( empty( $arg['value'] ) || ! is_string( $arg['value'] ) ) {
					continue;
				}

				if ( $arg['value'] !== $marker ) {
					continue;
				}

				$ids = ! empty( $request['front_store__in'] ) && is_array( $request['front_store__in'] )
					? array_filter( array_map( 'absint', $request['front_store__in'] ) )
					: array();
				$query->final_query['args'][ $i ]['value'] = implode( ',', $ids );
				$replaced = true;
				break;
			}
		}

		return $replaced;
	}
}
