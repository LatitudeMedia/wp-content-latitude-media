<?php

use \Elementor\Core\DynamicTags\Manager;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class Jet_Listing_Dynamic_Widget extends \Elementor\Widget_Base {

	use \Jet_Engine\Modules\Performance\Traits\Prevent_Wrap;

	private $jet_active_settings  = null;
	private $jet_dynamic_settings = null;
	private $jet_settings         = null;

	public function has_widget_inner_wrapper(): bool {
		if ( ! \Elementor\Plugin::instance()->experiments->is_feature_active( 'e_optimized_markup' ) ) {
			return true;
		}

		return apply_filters( 'jet-engine/listing/dynamic-widget/has-inner-wrapper', false, $this );
	}

	public function jet_with_defaults( $settings = array() ) {
		return array_merge( Jet_Elementor_Widgets_Storage::instance()->get_widget_defaults( $this ), $settings );
	}

	public function jet_settings( $setting = null ) {

		if ( null === $this->jet_settings ) {
			$this->jet_settings = $this->jet_with_defaults( $this->get_data( 'settings' ) );
		}

		$this->jet_settings['__je_has_widget_inner_wrapper'] = $this->has_widget_inner_wrapper();

		return self::get_items( $this->jet_settings, $setting );

	}

}