<?php
namespace Jet_Engine\Modules\Maps_Listings\Geocode_Providers;

class OpenStreetMap extends Base {

	public function build_api_url( $location ) {
		return add_query_arg(
			apply_filters( 'jet-engine/maps-listings/autocomplete-url-args/openstreetmap', array(
				'q'      => urlencode( $location ),
				'format' => 'json',
			) ),
			'https://nominatim.openstreetmap.org/search'
		);
	}

	/**
	 * Build Reverse geocoding API URL for given coordinates point
	 * @return [type] [description]
	 */
	public function build_reverse_api_url( $point = array() ) {
		return add_query_arg( array(
			'lat'    => $point['lat'],
			'lon'    => $point['lng'],
			'format' => 'json',
		), 'https://nominatim.openstreetmap.org/reverse' );
	}

	/**
	 * Build Autocomplete API URL for given place predictions
	 * @return mixed
	 */
	public function build_autocomplete_api_url( $query = '' ) {
		return false;
	}

	/**
	 * Find location name in the reverse geocoding response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_location_from_response_data( $data = array() ) {
		return isset( $data['display_name'] ) ? $data['display_name'] : false;
	}

	/**
	 * Find coordinates in the response data and return it
	 *
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function extract_coordinates_from_response_data( $data = array() ) {

		if ( is_array( $data ) && count( $data ) === 1 && isset( $data['message'] ) ) {
			$this->save_error( $data, 'geocode' );
			return false;
		}

		$coord = isset( $data[0] )
			? array( 'lat' => $data[0]['lat'], 'lng' => $data[0]['lon'] )
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
		return false;
	}

	/**
	 * Returns some important information that should be shown in Map Field
	 * @return string
	 */
	public function get_map_field_notice() {
		return esc_html( 'The OpenStreetMap provider does not support address autocomplete. You may set a location with a marker, or enter coordinates in \'lat, lng\' format ( e.g. \'46.967412120070094, 31.980971638597303\') into the search field and select the point from the dropdown.', 'jet-engine' );
	}

	/**
	 * Returns provider system slug
	 *
	 * @return [type] [description]
	 */
	public function get_id() {
		return 'openstreetmap';
	}

	/**
	 * Returns provider human-readable name
	 *
	 * @return [type] [description]
	 */
	public function get_label() {
		return __( 'OpenStreetMap', 'jet-engine' );
	}

	/**
	 * Provider-specific settings fields template
	 *
	 * @return [type] [description]
	 */
	public function settings_fields() {
		?>
		<template
			v-if="'openstreetmap' === settings.geocode_provider"
		>
			<cx-vui-component-wrapper
				label="<?php _e( 'Note:', 'jet-engine' ); ?>"
				description="<?php _e( 'Be aware that this service runs on donated servers and has a very limited capacity. Please avoid heavy usage (an absolute maximum of 1 request per second). Autocomplete requests are forbidden, so Map Field supports only coordinates mode or picking address on the map, and the Location & Distance filter will not work.', 'jet-engine' ); ?>"
			></cx-vui-component-wrapper>
		</template>
		<?php
	}

}
