<?php
/**
 * Base class for listing renderers
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Render_Base' ) ) {

	abstract class Jet_Engine_Render_Base {

		use \Jet_Engine\Modules\Performance\Traits\Prevent_Wrap;
		use \Jet_Engine_Setup_Listing_Trait;

		private $settings = null;

		public function __construct( $settings = array() ) {
			$parsed_settings = $this->get_parsed_settings( $settings );
			$this->settings  = apply_filters( 'jet-engine/listing/render/'. $this->get_name() . '/settings', $parsed_settings, $this );
		}

		public function get_settings( $setting = null ) {
			if ( $setting ) {
				return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : false;
			} else {
				return $this->settings;
			}
		}

		/**
		 * Returns parsed settings
		 *
		 * @param  array $settings
		 * @return array
		 */
		public function get_parsed_settings( $settings = array() ) {
			$defaults = $this->default_settings();
			$settings = wp_parse_args( $settings, $defaults );

			foreach ( $defaults as $key => $default_value ) {
				if ( null === $settings[ $key ] ) {
					$settings[ $key ] = $default_value;
				}
			}

			return $settings;
		}

		/**
		 * Returns plugin default settings
		 *
		 * @return array
		 */
		public function default_settings() {
			return array();
		}

		/**
		 * Returns required settings
		 *
		 * @return array
		 */
		public function get_required_settings() {
			$required = array();
			$settings = $this->get_settings();
			$default  = $this->default_settings();

			foreach ( $default as $key => $value ) {
				if ( isset( $settings[ $key ] ) ) {
					$required[ $key ] = $settings[ $key ];
				}
			}

			return $required;
		}

		public function get_default_cb_settings() {

			$settings   = array();
			$disallowed = array( 'checklist_divider_color' );

			foreach ( jet_engine()->listings->get_callbacks_args() as $key => $args ) {

				if ( in_array( $key, $disallowed ) ) {
					continue;
				}

				$settings[ $key ] = isset( $args['default'] ) ? $args['default'] : null;
			}

			return $settings;
		}

		public function get( $setting = null, $default = false ) {
			if ( isset( $this->settings[ $setting ] ) ) {
				return $this->settings[ $setting ];
			} else {
				$defaults = $this->default_settings();
				return isset( $defaults[ $setting ] ) ? $defaults[ $setting ] : $default;
			}
		}

		public function get_content() {
			ob_start();
			$this->render_content();
			return ob_get_clean();
		}

		public function get_wrapper_classes() {

			$base_class = $this->get_name();
			$settings   = $this->get_settings();
			$classes    = array(
				'jet-listing',
				$base_class,
			);

			if ( ! empty( $settings['className'] ) ) {
				$classes[] = esc_attr( $settings['className'] );
			}

			return $classes;

		}

		abstract public function get_name();

		/**
		 * Render listing item content
		 *
		 * @return [type] [description]
		 */
		abstract public function render();

		/**
		 * Call the render function from the exact Render instance
		 * @return [type] [description]
		 */
		public function render_content() {

			/**
			 * General hook fires before any JetEngine element render in any builder
			 */
			do_action( 'jet-engine/listing-element/before-render', $this );

			/**
			 * Specific hook for each JetEngine element fires before this element render
			 */
			do_action( 'jet-engine/listing-element/before-render/' . $this->get_name(), $this );

			$this->render();

			jet_engine()->frontend->footer_styles();
			jet_engine()->frontend->frontend_scripts();
		}

	}

}
