<?php

namespace Jet_Engine\Compatibility\Packages\Jet_Engine_WPML_Package\Maps_listings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Manager {

	/**
	 * A reference to an instance of this class.
	 *
	 * @access private
	 * @var    object
	 */
	private static $instance = null;

	/**
	 * A reference to an instance of compatibility package.
	 *
	 * @var \Jet_Engine\Compatibility\Packages\Jet_Engine_WPML_Package\Package
	 */
	private $package;

	private function __construct( $package = null ) {
		$this->package = $package;

		if ( ! jet_engine()->modules->is_module_active( 'maps-listings' ) ) {
			return;
		}
		
		add_filter( 'wpml_elementor_widgets_to_translate', array( $this, 'add_translatable_nodes' ) );
	}

	public function add_translatable_nodes( $nodes ) {

		$nodes['jet-smart-filters-location-distance'] = array(
			'conditions' => array(
				'widgetType' => 'jet-smart-filters-location-distance'
			),
			'fields'     => array(
				array(
					'field'       => 'placeholder',
					'type'        => esc_html__( 'Field: Placeholder', 'jet-engine' ),
					'editor_type' => 'LINE',
				),
				array(
					'field'       => 'geolocation_placeholder',
					'type'        => esc_html__( 'Field: User Geolocation Placeholder', 'jet-engine' ),
					'editor_type' => 'LINE',
				),
			),
		);

		return $nodes;

	}

	/**
	 * Returns the instance.
	 *
	 * @access public
	 * @return object
	 */
	public static function instance( $package = null ) {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self( $package );
		}

		return self::$instance;

	}
	
}
