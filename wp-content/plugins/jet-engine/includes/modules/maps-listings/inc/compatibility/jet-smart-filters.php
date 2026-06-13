<?php
namespace Jet_Engine\Modules\Maps_Listings\Compatibility;

class Jet_Smart_Filters {

	/**
	 * @var string JetSmartFilters request signature; captured before verification, as it is deleted after the request
	 */
	private $signature = '';

	public function __construct() {
		add_action( 'jet-smart-filters/render/ajax/before', array( $this, 'save_signature' ) );
		add_filter( 'jet-smart-filters/render/ajax/verify-signature', array( $this, 'check_signature' ) );
		add_filter( 'jet-smart-filters/query/request', array( $this, 'unslash_request' ), 1000, 2 );
	}

	public function unslash_request( $request, $query ) {
		if ( ! $query->is_ajax_filter() ) {
			return $request;
		}

		if ( empty( $request['settings'] ) ) {
			return $request;
		}

		$request['settings'] = $this->unslash_settings( $request['settings'] );

		return $request;
	}

	public function unslash_settings( $settings ) {
		$multiple_markers = $settings['multiple_markers'] ?? array();

		if ( ! empty( $multiple_markers ) ) {
			foreach ( $multiple_markers as $i => $marker ) {
				foreach ( $marker as $prop => $value ) {
					if ( is_string( $value ) ) {
						$multiple_markers[ $i ][ $prop ] = stripslashes( $value );
					}
				}
			}
	
			$settings['multiple_markers'] = $multiple_markers;
		}

		$to_unslash = array(
			'marker_icon',
			'marker_label_custom_output',
		);

		$to_unslash = apply_filters( 'jet-engine/maps-listings/compatibility/jet-smart-filters/unslash', $to_unslash );

		foreach ( $to_unslash as $setting ) {
			if ( ! empty( $settings[ $setting ] ) && is_string( $settings[ $setting ] ) ) {
				$settings[ $setting ] = stripslashes( $settings[ $setting ] );
			}
		}

		return $settings;
	}

	public function save_signature() {
		if ( ! empty( $_REQUEST['settings']['jsf_signature'] ) ) {
			$this->signature = $_REQUEST['settings']['jsf_signature'];
		}
	}

	public function check_signature( $result ) {

		if ( empty( $this->signature ) || empty( $_REQUEST['settings'] ) ) {
			return $result;
		}

		if ( false === strpos( $_REQUEST['provider'] ?? '', 'jet-engine-maps' ) ) {
			return $result;
		}

		$settings = $_REQUEST['settings'];

		unset( $settings['jsf_signature'] );

		$settings = $this->unslash_settings( $settings );
		
		$check_signature = jet_smart_filters()->render->create_signature( $settings );

		$result = $check_signature === $this->signature;

		return $result;
	}

}
