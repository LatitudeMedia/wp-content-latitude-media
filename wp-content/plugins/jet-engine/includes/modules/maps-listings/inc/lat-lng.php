<?php
namespace Jet_Engine\Modules\Maps_Listings;

class Lat_Lng {

	public $meta_key          = '_jet_maps_coord';
	public $field_groups      = array();
	public $done              = false;
	public $failures          = array();
	public $current_source    = null;

	private $error_prefix = 'je-coord-error';
	private $geocode_provider = null;
	private $error_timeout = DAY_IN_SECONDS;

	/**
	 * Constructor for the class
	 */
	public function __construct() {
		$this->error_timeout = apply_filters( 'jet-engine/maps-listing/error_timeout', $this->error_timeout );

		if ( ! is_numeric( $this->error_timeout ) || $this->error_timeout < 300 ) {
			$this->error_timeout = 300;
		}

		add_action( 'init', array( $this, 'hook_preload' ) );
	}

	/**
	 * Set current source
	 *
	 * @param $source
	 */
	public function set_current_source( $source ) {
		$this->current_source = $source;
	}

	/**
	 * Set $done to false to be able to run preload_groups() more than once
	 */
	public function not_done() {
		$this->done = false;
	}

	/**
	 * Get error prefix
	 *
	 * @return string Error prefix
	 */
	public function get_error_prefix() {
		return $this->error_prefix;
	}

	/**
	 * Get source instance
	 *
	 * @return false|Source\Base
	 */
	public function get_source_instance() {

		if ( ! $this->current_source ) {
			return false;
		}

		return Module::instance()->sources->get_source( $this->current_source );
	}

	/**
	 * Hook meta-fields preloading
	 *
	 * @return void
	 */
	public function hook_preload() {

		$preload = Module::instance()->settings->get( 'enable_preload_meta' );

		if ( ! $preload ) {
			return;
		}

		$preload_fields = Module::instance()->settings->get( 'preload_meta' );

		if ( empty( $preload_fields ) ) {
			return;
		}

		$preload_fields = explode( ',', $preload_fields );
		$preload_fields = array_map( 'trim', $preload_fields );

		$sources = Module::instance()->sources->get_sources();

		if ( empty( $sources ) ) {
			return;
		}

		$engine_fields = [];
		$custom_fields = [];

		foreach ( $preload_fields as $field ) {
			if ( false === strpos( $field, '_custom::' ) ) {
				$engine_fields[] = $field;
			} else {
				$custom_fields[] = $field;
			}
		}

		foreach ( $sources as $source ) {

			// Preload non-Engine fields
			if ( $source->is_custom() && ! empty( $custom_fields ) ) {
				$source->preload_hooks( $custom_fields );
			}

			// Preload JetEngine fields
			if ( ! $source->is_custom() && ! empty( $engine_fields ) ) {
				$source->preload_hooks( $engine_fields );
			}

		}

	}

	/**
	 * Get address value from post object and field name
	 *
	 * @param object $post  Post object.
	 * @param string $field Field name.
	 *
	 * @return mixed
	 */
	public function get_address_from_field( $post, $field ) {

		$source = $this->get_source_instance();

		if ( $source ) {
			return $source->get_field_value( $post, $field );
		}

		// For backward compatibility.
		return apply_filters( 'jet-engine/maps-listing/get-address-from-field', false, $post, $field );
	}

	/**
	 * Get address string from post object and field names array
	 *
	 * @param object $post   Post object.
	 * @param array  $fields Fields array.
	 *
	 * @return bool|string
	 */
	public function get_address_from_fields_group( $post = null, $fields = array() ) {

		$group = array();

		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return false;
		}

		foreach ( $fields as $field ) {
			if ( ! empty( $_POST[ $field ] ) ) {
				$group[] = $_POST[ $field ];
			} else {
				$group[] = $this->get_address_from_field( $post, $field );
			}
		}

		$group = array_filter( $group );

		if ( empty( $group ) ) {
			return false;
		} else {
			return implode( ', ', $group );
		}

	}

	/**
	 * Preload fields groups
	 */
	public function preload_groups( $post_id ) {

		if ( $this->done ) {
			return;
		}

		$group = false;
		$post  = false;

		$source = $this->get_source_instance();

		if ( $source ) {
			$group = $source->get_field_groups();
			$post  = $source->get_obj_by_id( $post_id );
		}

		if ( empty( $group ) || empty( $post ) ) {
			return;
		}

		foreach ( $group as $fields ) {

			$address = $this->get_address_from_fields_group( $post, $fields );

			if ( ! $address ) {
				continue;
			}

			$coord = $this->get( $post, $address, implode( '+', $fields ) );

		}

		do_action( 'jet-engine/maps-listings/preload/after-group-preload', $post_id );

		$this->done = true;

	}

	/**
	 * Preload field address
	 *
	 * @param  int    $post_id
	 * @param  string $address
	 * @return void
	 */
	public function preload( $post_id, $address, $field = '' ) {

		if ( empty( $address ) ) {
			return;
		}

		$post   = false;
		$source = $this->get_source_instance();

		if ( $source ) {
			$post = $source->get_obj_by_id( $post_id );
		}

		$coord = $this->get( $post, $address, $field );
	}

	/**
	 * Get geocode provider
	 *
	 * @return Geocode_Providers\Base|false
	 */
	public function get_geocode_provider() {
		if ( isset( $this->geocode_provider ) ) {
			return $this->geocode_provider;
		}

		$provider_id            = Module::instance()->settings->get( 'geocode_provider' );
		$this->geocode_provider = Module::instance()->providers->get_providers( 'geocode', $provider_id );

		return $this->geocode_provider;
	}

	/**
	 * Returns remote coordinates by location
	 *
	 * @param  [type] $location [description]
	 * @return [type]           [description]
	 */
	public function get_remote( $location ) {

		$decoded_location = json_decode( htmlspecialchars_decode( $location ), true );

		if ( $decoded_location && $decoded_location['lat'] && $decoded_location['lng'] ) {
			return $decoded_location;
		}

		$geocode_provider = $this->get_geocode_provider();

		if ( ! $geocode_provider ) {
			return false;
		} else {
			return $geocode_provider->get_location_data( $location );
		}

	}

	/**
	 * Get not-post related coordinates
	 *
	 * @param  [type] $location [description]
	 * @return [type]           [description]
	 */
	public function get_from_transient( $location ) {

		$key   = md5( $location );
		$coord = get_transient( $key );

		if ( ! $coord ) {

			$coord = $this->get_remote( $location );

			if ( $coord ) {
				set_transient( $key, $coord, WEEK_IN_SECONDS );
			}

		}

		return is_array( $coord ) ? array_map( 'floatval', $coord ) : $coord;

	}

	/**
	 * Prints failures message
	 */
	public function failures_message() {

		if ( empty( $this->failures ) ) {
			return;
		}

		if ( 5 <= count( $this->failures ) ) {
			$message = __( 'We can`t get coordinates for multiple locations', 'jet-engine' );
		} else {

			$locations = array();

			foreach ( $this->failures as $key => $location ) {
				$locations[] = sprintf( '%1$s (%2$s)', $location, $key );
			}

			$message = __( 'We can`t get coordinates for locations: ', 'jet-engine' ) . implode( ', ', $locations );

		}

		$message .= __( '. Please check your API key (you can validate it in maps settings or check in Google Console), make sure Geocoding API is enabled.', 'jet-engine' );

		return sprintf( '<div style="border: 1px solid #f00; color: #f00;  padding: 20px; margin: 10px 0;">%s</div>', $message );

	}

	public function maybe_add_offset( $coordinates = array() ) {

		if ( ! $this->is_valid_coordinates( $coordinates ) ) {
			return false;
		}

		$add_offset = Module::instance()->settings->get( 'add_offset' );

		if ( ! $add_offset ) {
			return $coordinates;
		}

		$offset_rate = apply_filters( 'jet-engine/maps-listing/offset-rate', 100000 );

		$offset_lat = ( 10 - rand( 0, 20 ) ) / $offset_rate;
		$offset_lng = ( 10 - rand( 0, 20 ) ) / $offset_rate;

		if ( isset( $coordinates['lat'] ) ) {
			$coordinates['lat'] = floatval( $coordinates['lat'] ) + $offset_lat;
		}

		if ( isset( $coordinates['lng'] ) ) {
			$coordinates['lng'] = floatval( $coordinates['lng'] ) + $offset_lng;
		}

		return $coordinates;

	}

	public function is_error_coordinates( $coordinates ) {
		if ( ! is_array( $coordinates ) || ! isset( $coordinates['lat'] ) || ! isset( $coordinates['lng'] ) ) {
			return false;
		}

		$lat = $coordinates['lat'];

		return false !== strpos( $lat, $this->error_prefix );
	}

	/**
	 * Make array of invalid values, signifying empty result or provider API error
	 *
	 * @param array $type 'empty' or 'api_error'
	 *
	 * @return array{lat: string, lng: string}
	 */
	public function make_error_coordinates_array( $type = '', $timestamp = false ) {
		$result = array(
			'lat' => $this->error_prefix,
			'lng' => $this->error_prefix,
		);

		$geocode_provider = $this->get_geocode_provider();

		if ( ! $geocode_provider ) {
			return $result;
		}

		$provider_id = $geocode_provider->get_id();

		switch ( $type ) {
			case 'empty':
				$result['lat'] .= ":{$provider_id}:empty";
				$result['lng'] .= ":{$provider_id}:empty";
				break;
			case 'api_error':
				$t = ! empty( $timestamp ) ? $timestamp : time();
				$result['lat'] .= ":{$provider_id}:{$t}";
				$result['lng'] .= ":{$provider_id}:{$t}";
				break;
		}

		return $result;
	}

	public function is_valid_coordinates( $coordinates ) {
		if ( ! is_array( $coordinates ) || ! isset( $coordinates['lat'] ) || ! isset( $coordinates['lng'] ) ) {
			return false;
		}

		$lat = $coordinates['lat'];
		$lng = $coordinates['lng'];

		if ( ! is_numeric( $lat ) || ! is_numeric( $lng ) ) {
			return false;
		}

		if ( abs( $lat ) > 90 || abs( $lng ) > 180 ) {
			return false;
		}

		return true;
	}

	/**
	 * Returns lat and lang for passed address
	 *
	 * @param  int|object $post     Post ID or object
	 * @param  string     $location Location
	 *
	 * @return array|bool
	 */
	public function get( $post, $location, $field_name = '' ) {

		if ( ! $location ) {
			return false;
		}

		if ( is_array( $location ) ) {
			return $this->maybe_add_offset( $location );
		}

		$location_hash = md5( $location );
		$source        = $this->get_source_instance();

		if ( ! $source ) {
			return false;
		}

		$field_name = apply_filters( 'jet-engine/maps-listing/preload/field-name', $field_name, $post );

		$meta = $source->get_field_coordinates( $post, $location, $field_name );

		$stored_hash        = $meta['key'] ?? false;
		$stored_coordinates = $meta['coord'] ?? array();

		if ( ! empty( $meta ) && $location_hash === $stored_hash && $this->is_valid_coordinates( $stored_coordinates ) ) {
			return $this->maybe_add_offset( $stored_coordinates );
		}

		$coord = false;
		$try   = ! $this->is_error_coordinates( $stored_coordinates );

		$retry        = false;
		$last_request = false;
		$retry_type   = 'empty';

		$geocode_provider = $this->get_geocode_provider();

		if ( ! $try && ! empty( $stored_coordinates['lat'] ) && $geocode_provider ) {
			$parts = explode( ':', $stored_coordinates['lat'] );

			if ( ( $parts[2] ?? '' ) === 'empty' ) {
				$retry = $geocode_provider->get_id() !== $parts[1];
			} else {
				$retry = $geocode_provider->get_id() !== $parts[1]
					       || ( \Jet_Engine_Tools::is_valid_timestamp( $parts[2] ?? '' ) && time() - $parts[2] > $this->error_timeout );
				$retry_type = 'api_error';

				if ( $retry ) {
					$last_request = time();
				} else {
					$last_request = $parts[2] ?? false;
				}
			}

			$try = $retry || $stored_hash !== $location_hash;
		}

		if ( $try ) {
			$coord = $this->get_remote( $location );
		}

		if ( ! $coord ) {
			$this->add_failure( $post, $location );

			if ( $geocode_provider && ! empty( $geocode_provider->get_error( 'geocode' ) ) || $retry_type === 'api_error' ) {
				$coord = $this->make_error_coordinates_array( 'api_error', $last_request );
			} else {
				$coord = $this->make_error_coordinates_array( 'empty' );
			}
		}

		if ( ! $field_name ) {
			$field_name = $this->meta_key;
		}

		$this->update_address_coord_field( $post, $field_name, $location_hash, $coord );

		return $this->maybe_add_offset( $coord );

	}

	public function add_failure( $post, $location ) {

		$key    = false;
		$source = $this->get_source_instance();

		if ( $source ) {
			$key = $source->get_failure_key( $post );
		}

		// For backward compatibility.
		if ( ! $key ) {
			$key = apply_filters( 'jet-engine/maps-listing/failure-message-key', $key, $post );
		}

		if ( ! $key ) {
			return;
		}

		$this->failures[ $key ] = $location;
	}

	public function update_address_coord_field( $post, $field_name, $location_hash, $coord ) {

		$field_name = apply_filters( 'jet-engine/maps-listing/preload/field-name', $field_name, $post );

		$value = array(
			'key'   => $location_hash,
			'coord' => $coord,
		);

		$source = $this->get_source_instance();

		if ( $source ) {
			$source->update_field_value( $post, $field_name, $value );
			return;
		}

		// For backward compatibility.
		do_action( 'jet-engine/maps-listings/update-address-coord-field', $post, $value, $this );
	}

}
