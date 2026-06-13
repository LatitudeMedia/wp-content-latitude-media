<?php
namespace Jet_Engine\Modules\Maps_Listings\Geocode_Providers;

use Jet_Engine\Modules\Maps_Listings\Module;

class Google extends Base {

	private $places_api_status_key = 'jet-engine-maps-places-api-status';

	public function get_api_key( $for_geocoding = false ) {
		$api_key           = Module::instance()->settings->get( 'api_key' );
		$use_geocoding_key = Module::instance()->settings->get( 'use_geocoding_key' );
		$geocoding_key     = Module::instance()->settings->get( 'geocoding_key' );

		// from 3.0.0 map could have different providers so we need to reset some data if provider is not Google maps
		if ( $for_geocoding && 'google' !== Module::instance()->settings->get( 'map_provider' ) ) {
			$use_geocoding_key = true;
			$api_key           = false;
		}

		if ( $use_geocoding_key && $geocoding_key ) {
			$api_key = $geocoding_key;
		}

		return $api_key;
	}

	public function base_api_url() {

		$api_url = 'https://maps.googleapis.com/maps/api/geocode/json';
		$api_key = $this->get_api_key( true );

		// Do nothing if api key not provided
		if ( ! $api_key ) {
			return false;
		}

		return add_query_arg(
			array(
				'key'      => urlencode( $api_key ),
				'language' => substr( get_bloginfo( 'language' ), 0, 2 ),
			),
			$api_url
		);
	}

	public function build_api_url( $location = '' ) {
		return add_query_arg( array(
			'address' => urlencode( $location ),
		), $this->base_api_url() );
	}

	/**
	 * Build Reverse geocoding API URL for given coordinates point
	 * @return [type] [description]
	 */
	public function build_reverse_api_url( $point = array() ) {
		return add_query_arg( array(
			'latlng' => implode( ',', $point ),
		), $this->base_api_url() );
	}

	/**
	 * Build Autocomplete API URL for given place predictions
	 * @return mixed
	 */
	public function build_autocomplete_api_url( $query = '' ) {

		$api_url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
		$api_key = $this->get_api_key( true );

		if ( ! $api_key ) {
			return false;
		}

		return add_query_arg(
			apply_filters( 'jet-engine/maps-listings/autocomplete-url-args/google', array(
				'input'    => urlencode( $query ),
				'key'      => urlencode( $api_key ),
				'language' => substr( get_bloginfo( 'language' ), 0, 2 ),
				//'sessiontoken' => '', // todo - add sessiontoken to optimize request.
			) ),
			$api_url
		);
	}

	public function get_autocomplete_data( $query = '' ) {

		if ( ! $query ) {
			return false;
		}

		$legacy_status = $this->get_legacy_status();

		switch ( $legacy_status ) {
			case 'none':
				$data = false;
				break;
			case 'legacy':
				$data = $this->get_legacy_autocomplete_data( $query );
				break;
			case 'new':
				$data = $this->get_new_autocomplete_data( $query );
				break;
			case 'needs_updating':
				$data = $this->get_autocomplete_data_with_status_update( $query );
				break;
		}
		
		return $data;
	}

	public function get_legacy_autocomplete_data( $query = '' ) {
		
		if ( ! $query ) {
			return false;
		}

		return parent::get_autocomplete_data( $query );
	}

	public function get_new_autocomplete_data( $query = '' ) {

		if ( ! $query ) {
			return false;
		}
		
		$api_url = 'https://places.googleapis.com/v1/places:autocomplete';
		
		$api_key = $this->get_api_key( true );
		
		$body = array(
			'input'        => $query,
			'languageCode' => substr( get_bloginfo( 'language' ), 0, 2 ),
		);

		$body = json_encode( apply_filters( 'jet-engine/maps-listings/autocomplete-request-body/google', $body ) );

		$args['headers'] = array(
			'X-Goog-Api-Key' => $api_key,
			'Content-Type'   => 'application/json',
		);

		$response = wp_remote_post( $api_url, array(
			'body'    => $body,
			'headers' => $args['headers'],
		) );

		$json = wp_remote_retrieve_body( $response );

		$data = json_decode( $json, true );

		if ( ! empty( $data['error'] ) ) {
			$this->save_error( $data, 'autocomplete' );
		}

		return $this->extract_autocomplete_data_from_response_data( $data );
	}

	public function has_error( $type = 'autocomplete' ) {
		$error = $this->get_error( $type );

		if ( ! $error ) {
			return false;
		}

		switch ( $type ) {
			case 'autocomplete':
				$status = $error['status'] ?? '';

				if ( $status === 'REQUEST_DENIED' ) {
					return true;
				}

				$status = $error['error']['status'] ?? '';
				
				if ( $status === 'PERMISSION_DENIED' ) {
					return true;
				}

			break;
		}

		return false;
	}

	public function set_legacy_status( $status ) {
		$value = time() . ':' . $status;
		update_option( $this->places_api_status_key, $value );
		return $status;
	}

	public function get_legacy_status( $type = 'autocomplete', $get_original = false ) {
		$value = get_option( $this->places_api_status_key, false );

		if ( ! $value ) {
			return 'needs_updating';
		}

		$value = explode( ':', $value );
		
		$time   = ( int ) $value[0];
		$status = $value[1];

		if ( ! $get_original && time() - $time > $this->get_legacy_status_timeout( $type ) ) {
			return 'needs_updating';
		}

		return $status;
	}

	public function get_legacy_status_timeout( $type = 'autocomplete' ) {
		$timeouts = array(
			'autocomplete' => 300,
		);

		return $timeouts[ $type ] ?? 300;
	}

	public function get_autocomplete_data_with_status_update( $query = '' ) {
		if ( ! $query ) {
			return false;
		}

		$status = $this->get_legacy_status( 'autocomplete', true );

		if ( $status === 'none' ) {
			$status = 'new';
		}
		$status = 'new';
		if ( $status === 'new' ) {
			$data = $this->get_new_autocomplete_data( $query );
		} else {
			$data = $this->get_legacy_autocomplete_data( $query );
		}

		if ( false === $data && $this->has_error( 'autocomplete' ) ) {
			$status = $status === 'new' ? 'legacy' : 'new';

			$this->clear_error( 'autocomplete' );

			if ( $status === 'new' ) {
				$data = $this->get_new_autocomplete_data( $query );
			} else {
				$data = $this->get_legacy_autocomplete_data( $query );
			}
		}
		
		$this->set_legacy_status( false === $data && $this->has_error( 'autocomplete' ) ? 'none' : $status );

		return $data;
	}

	/**
	 * Find location name in the reverse geocoding response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_location_from_response_data( $data = array() ) {
		return isset( $data['results'][0]['formatted_address'] ) ? $data['results'][0]['formatted_address'] : false;
	}

	/**
	 * Find coordinates in the response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_coordinates_from_response_data( $data = array() ) {

		if ( isset( $data['error'] ) || isset( $data['error_message'] ) ) {
			$this->save_error( $data, 'geocode' );
			return false;
		}

		$coord = isset( $data['results'][0]['geometry']['location'] )
			? $data['results'][0]['geometry']['location']
			: false;

		if ( ! $coord ) {
			return false;
		}

		return $coord;

	}

	/**
	 * Find place predictions in the response data and return it
	 *
	 * @param  array $data
	 * @return array|false
	 */
	public function extract_autocomplete_data_from_response_data( $data = array() ) {

		$predictions = isset( $data['predictions'] ) ? $data['predictions'] : false;

		$new_api = false;

		if ( ! $predictions && isset( $data['suggestions'] ) ) {
			$predictions = $data['suggestions'] ?? false;
			$new_api = true;
		}

		if ( ! $predictions ) {
			return false;
		}
		
		$result = array();

		foreach ( $predictions as $prediction ) {
			if ( ! $new_api ) {
				$result[] = array(
					'address' => $prediction['description'] ?? '',
				);
			} else {
				$result[] = array(
					'address' => $prediction['placePrediction']['text']['text'] ?? '',
				);
			}
		}

		return $result;
	}

	/**
	 * Settings assets
	 *
	 * @return [type] [description]
	 */
	public function settings_assets() {

		wp_enqueue_script(
			'jet-engine-maps-settings-google',
			jet_engine()->plugin_url( 'includes/modules/maps-listings/assets/js/admin/settings-google.js' ),
			array( 'cx-vue-ui' ),
			jet_engine()->get_version(),
			true
		);

	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'google';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'Google', 'jet-engine' );
	}

	/**
	 * Provider-specific settings fields template
	 *
	 * @return [type] [description]
	 */
	public function settings_fields() {
		?>
		<template
			v-if="'google' === settings.geocode_provider"
		>
			<template v-if="'google' === settings.map_provider">
				<cx-vui-switcher
					label="<?php _e( 'Separate Geocoding API key', 'jet-engine' ); ?>"
						description="<?php _e( 'Use separate key for Geocoding API. This allows you to set more accurate restrictions for your API key.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					@input="updateSetting( $event, 'use_geocoding_key' )"
					:value="settings.use_geocoding_key"
				></cx-vui-switcher>
				<cx-vui-input
					label="<?php _e( 'Geocoding API Key', 'jet-engine' ); ?>"
					description="<?php _e( 'Google maps API key with <b>Geocoding API</b> and <b>Places API</b> enabled. For this key <b>Application restrictions</b> should be set to <b>None</b> or <b>IP addresses</b> and in the <b>API restrictions</b> you need to select <b>Don\'t restrict key</b> or enable <b>Geocoding API</b> and <b>Places API</b>.<br><br><b>Places API</b> is required for Location filter and Map field, to allow search of places matching query string.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					@on-input-change="updateSetting( $event.target.value, 'geocoding_key' )"
					:value="settings.geocoding_key"
					v-if="settings.use_geocoding_key"
				></cx-vui-input>
			</template>
			<template v-else>
				<cx-vui-input
					label="<?php _e( 'Geocoding API Key', 'jet-engine' ); ?>"
					description="<?php _e( 'Google maps API key with <b>Geocoding API</b> and <b>Places API</b> enabled. For this key <b>Application restrictions</b> should be set to <b>None</b> or <b>IP addresses</b> and in the <b>API restrictions</b> you need to select <b>Don\'t restrict key</b> or enable <b>Geocoding API</b> and and <b>Places API</b>.<br><br><b>Places API</b> is required for Location filter and Map field, to allow search of places matching query string.', 'jet-engine' ); ?>"
					:wrapper-css="[ 'equalwidth' ]"
					size="fullwidth"
					@on-input-change="updateSetting( $event.target.value, 'geocoding_key' )"
					:value="settings.geocoding_key"
				></cx-vui-input>
			</template>
			<jet-engine-maps-google-validate-api-key
				:settings="settings"
			/>
		</template>
		<?php
	}

}
