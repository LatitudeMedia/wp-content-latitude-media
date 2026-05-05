<?php
namespace Jet_Engine\Modules\Data_Stores\Stores;

class User_Meta_Store extends Base_Store {

	public $is_int = false;

	/**
	 * Store type ID
	 */
	public function type_id() {
		return 'user-meta';
	}

	/**
	 * Store type name
	 */
	public function type_name() {
		return __( 'User Metadata', 'jet-engine' );
	}

	public function on_init() {
		$this->set_store_as_string();
	}

	/**
	 * Add to store callback
	 */
	public function add_to_store( $store_id, $post_id ) {

		if ( ! is_user_logged_in() ) {
			return;
		}

		$store = $this->get( $store_id );

		if ( ! in_array( $post_id, $store ) ) {
			$store[] = $this->sanitize_store_item( $post_id );
		}

		$count = count( $store );

		$this->set_store( $store_id, $store );

		return $count;
	}

	/**
	 * Add to store callback
	 */
	public function remove( $store_id, $post_id ) {

		if ( ! is_user_logged_in() ) {
			return;
		}

		$store = $this->get( $store_id );

		if ( false !== ( $index = array_search( $post_id, $store ) ) ) {
			unset( $store[ $index ] );
		}

		$store = array_values( $store ); // added for reindex of array

		$count = count( $store );

		$this->set_store( $store_id, $store );

		return $count;

	}

	public function set_store( $store_id, $store ) {

		$user_id = get_current_user_id();

		if ( is_array( $store ) ) {
			$store = array_map(
				function( $item ) {
					return is_scalar( $item ) ? strval( $item ) : $item;
				},
				$store
			);
		}

		update_user_meta( $user_id, $this->prefix . $store_id, $store );

	}

	/**
	 * Get post IDs from store
	 */
	public function get( $store_id ) {

		$user_id = apply_filters(
			'jet-engine/data-stores/store/user-meta/user-id',
			get_current_user_id(),
			$store_id
		);

		if ( ! $user_id ) {
			return array();
		}

		$store = get_user_meta( $user_id, $this->prefix . $store_id, true );

		if ( empty( $store ) ) {
			$store = array();
		}

		return apply_filters( 'jet-engine/data-stores/store/data', $store, $store_id );
	}

	/**
	 * Clear store.
	 *
	 * @param $store_id
	 */
	public function clear_store( $store_id = null ) {

		global $wpdb;

		$meta_key = $this->prefix . $store_id;

		$wpdb->delete(
			$wpdb->usermeta,
			array(
				'meta_key' => $meta_key,
			),
			array(
				'%s',
			)
		);

		return true;
	}

	/**
	 * Whether store supports clearing records
	 */
	public function supports_clearing() {
		return true;
	}
}
