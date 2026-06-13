<?php
/**
 * Class description
 *
 * @package   package_name
 * @author    Cherry Team
 * @license   GPL-2.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Engine_Frontend' ) ) {

	/**
	 * Define Jet_Engine_Frontend class
	 */
	class Jet_Engine_Frontend {

		protected $listing_id = null;
		protected $processed_listing_id = null;
		protected $did_scripts = false;
		private   $listing_assets_loaded = [];

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			$this->register_listing_styles();
			add_action( 'wp_enqueue_scripts', array( $this, 'register_listing_deps'), 9 );
		}

		public function register_slider_assets() {
			if ( jet_engine()->misc_settings->get_settings( 'slider_library' ) === 'slick' ) {
				wp_register_script(
					'jquery-slick',
					jet_engine()->plugin_url( 'assets/lib/slick/slick.min.js' ),
					array( 'jquery' ),
					'1.8.1',
					true
				);
			} elseif ( ! wp_script_is( 'swiper', 'registered' ) ) {
				wp_register_script(
					'swiper',
					jet_engine()->plugin_url( 'assets/lib/swiper/swiper.min.js' ),
					[],
					'8.4.5',
					true
				);

				wp_register_style(
					'swiper',
					jet_engine()->plugin_url( 'assets/lib/swiper/css/swiper.min.css' ),
					[],
					'8.4.5'
				);
			}
		}

		public function register_listing_deps() {
			$this->register_slider_assets();
			$this->register_jet_plugins_js();
		}

		public function register_jet_plugins_js() {
			jet_engine()->register_jet_plugins_js();
		}

		/**
		 * Register listing assets
		 *
		 * @return void
		 */
		public function register_listing_styles() {

			wp_register_style(
				'jet-engine-frontend',
				jet_engine()->plugin_url( 'assets/css/frontend.css' ),
				array(),
				jet_engine()->get_version()
			);

		}

		/**
		 * Enqueue front-end scripts
		 *
		 * @return void
		 */
		public function frontend_scripts() {

			if ( $this->did_scripts ) {
				return;
			}

			$this->did_scripts = true;

			$dependencies = array_merge(
				array( 'jquery', 'jet-plugins' ),
				apply_filters(
					'jet-engine/listings/frontend-scripts/dependencies',
					array()
				)
			);

			wp_enqueue_script(
				'jet-engine-frontend',
				jet_engine()->plugin_url( 'assets/js/frontend/frontend.js' ),
				$dependencies,
				jet_engine()->get_version(),
				true
			);

			do_action( 'jet-engine/listings/frontend-scripts' );

			$hover_action_timeout = apply_filters( 'jet-engine/map-popup/timeout', 400 ); // deprecated
			$hover_action_timeout = apply_filters( 'jet-engine/listings/custom-url-actions/hover-timeout', $hover_action_timeout );

			$root_object = jet_engine()->listings->objects_stack->get_root_object();

			$post_id = is_a( $root_object, 'WP_Post' ) ? $root_object->ID : 0;

			$localize_data = apply_filters( 'jet-engine/listing/frontend/js-settings', array(
				'ajaxurl'     => esc_url( admin_url( 'admin-ajax.php' ) ),
				'ajaxlisting' => $this->get_ajax_listing_url(),
				'restNonce'   => wp_create_nonce( 'wp_rest' ),
				'hoverActionTimeout' => $hover_action_timeout,
				'post_id' => $post_id,
				'sliderLibrary' => jet_engine()->misc_settings->get_settings( 'slider_library' ),
			) );

			if ( is_object( $root_object ) ) {
				$localize_data['queried_object_id']    = jet_engine()->listings->data->get_current_object_id( $root_object );
				$localize_data['queried_object_class'] = get_class( $root_object );
			}

			wp_localize_script( 'jet-engine-frontend', 'JetEngineSettings', $localize_data );

		}

		/**
		 * Get AJAX listing URL
		  *
		 * @return string
		 */
		public function get_ajax_listing_url( $action = null ) {

			global $wp;

			$query = '';

			// phpcs:disable
			if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
				$query .= '?' . $_SERVER['QUERY_STRING'];
			}
			// phpcs:enable

			$query_args = array( 'nocache' => 1 );

			if ( $action ) {
				$query_args['jet_engine_action'] = $action;
			}

			return apply_filters(
				'jet-engine/listings/ajax-listing-url',
				add_query_arg( $query_args, home_url( $wp->request . '/' . $query ) )
			);

		}

		/**
		 * Defines how CSS should be included. If true - styles included in footer only when JetEngine widgets was used,
		 * if false - styles always enqueued on wp_enqueue_scripts
		 * @return boolean [description]
		 */
		public function is_styles_in_footer() {
			return apply_filters( 'jet-engine/listings/styles-in-footer', false );
		}

		/**
		 * Enqueue front-end styles
		 *
		 * @return void
		 */
		public function frontend_styles() {
			if ( ! $this->is_styles_in_footer() ) {
				wp_enqueue_style( 'jet-engine-frontend' );
			}
		}

		/**
		 * Enqueue front-end styles in footer
		 *
		 * @return [type] [description]
		 */
		public function footer_styles() {
			if ( $this->is_styles_in_footer() ) {
				wp_enqueue_style( 'jet-engine-frontend' );
			}
		}

		/**
		 * Preview scripts
		 *
		 * @return void
		 */
		public function preview_scripts() {

			if ( jet_engine()->misc_settings->get_settings( 'slider_library' ) === 'slick' ) {
				wp_enqueue_script( 'jquery-slick' );
			} else {
				wp_enqueue_script( 'swiper' );
				wp_enqueue_style( 'swiper' );
			}
			

			$this->enqueue_masonry_assets();
			$this->frontend_scripts();

			wp_enqueue_style( 'jet-engine-frontend' );

			do_action( 'jet-engine/listings/preview-scripts' );

		}

		public function ensure_lib( $lib ) {

			$libs = array(
				'imagesloaded' => 'assets/lib/imagesloaded/imagesloaded.min.js',
			);

			if ( ! isset( $libs[ $lib ] ) ) {
				return;
			}

			if ( ! wp_script_is( $lib, 'registered' ) ) {
				wp_register_script(
					$lib,
					jet_engine()->plugin_url( $libs[ $lib ] ),
					array(),
					jet_engine()->get_version(),
					true
				);
			}

			if ( ! wp_script_is( $lib, 'enqueued' ) ) {
				wp_enqueue_script( $lib );
			}
		}

		/**
		 * Enqueues masonry assets
		 *
		 * @return void
		 */
		public function enqueue_masonry_assets() {

			$this->ensure_lib( 'imagesloaded' );

			wp_enqueue_script(
				'jet-engine-macy',
				jet_engine()->plugin_url( 'assets/lib/macy/macy.js' ),
				array(),
				jet_engine()->get_version(),
				true
			);
		}

		/**
		 * Set currently processing listing ID
		 *
		 * @param string|integer $listing_id
		 */
		public function set_listing( $listing_id = null ) {

			$this->listing_id = $listing_id;
			do_action( 'jet-engine/listings/setup', $this->listing_id );

		}

		/**
		 * Unset information about current listing
		 *
		 * @return void
		 */
		public function reset_listing() {

			$this->reset_data();

			do_action( 'jet-engine/listings/reset', $this->listing_id );

			$this->listing_id = null;

			jet_engine()->listings->did_posts->reset_currently_did_posts();

		}

		/**
		 * Returns currently processed listing id
		 * @return [type] [description]
		 */
		public function get_listing_id() {
			return $this->listing_id;
		}

		/**
		 * Get listing item content
		 *
		 * @param  $post
		 * @return string
		 */
		public function get_listing_item( $post ) {

			$this->setup_data( $post );

			$listing_id = apply_filters( 'jet-engine/listings/frontend/rendered-listing-id', $this->listing_id );
			$content = $this->get_listing_item_content( $listing_id );

			$content = apply_filters( 'jet-engine/listings/frontend/listing-item-content', $content, $listing_id, $post );

			do_action( 'jet-engine/listings/frontend/object-done', $post, $listing_id );

			return $content;

		}

		/**
		 * Ensures that listing item assets are loaded by getting listing item content.
		 * Useful when you don't need to actually render the listing item,
		 * but to make sure that scripts and styles are loaded.
		 * 
		 * @param  int         $listing_id
		 * @param  object|null $object
		 */
		public function ensure_listing_item_assets( $listing_id, $object = null ) {
			/**
			 * prevent loading assets multiple times for the same listing
			 * 
			 * @see https://github.com/Crocoblock/issues-tracker/issues/18476
			 */
			if ( isset( $this->listing_assets_loaded[ $listing_id ] ) ) {
				return;
			}

			$this->listing_assets_loaded[ $listing_id ] = true;

			if ( ! ( defined('REST_REQUEST') && REST_REQUEST ) && ! wp_doing_ajax() ) {
				$initial_listing_id = $this->listing_id;
				$this->listing_id   = absint( $listing_id );

				if ( ! $this->listing_id ) {
					$this->listing_id = $initial_listing_id;
					return;
				}

				$initial_object = jet_engine()->listings->data->get_current_object();

				global $post;
				$default_post = $post;
				$this->get_listing_item( $object );
				$post = $default_post;
				setup_postdata( $post );

				$this->listing_id = $initial_listing_id;
				jet_engine()->listings->data->set_current_object( $initial_object );
			}
		}

		public function ensure_slider_assets() {
			if ( jet_engine()->misc_settings->get_settings( 'slider_library' ) === 'slick' ) {
				wp_enqueue_script( 'jquery-slick' );
			} else {
				wp_enqueue_script( 'swiper' );
				wp_enqueue_style( 'swiper' );
			}
			
			$this->ensure_lib( 'imagesloaded' );

			do_action( 'jet-engine/ensure-assets/slider' );
		}

		/**
		 * Returns listing item content by listing ID
		 * @param  [type] $listing_id [description]
		 * @return [type]             [description]
		 */
		public function get_listing_item_content( $listing_id ) {

			$content = null;

			if ( ! $listing_id ) {
				return $content;
			}

			$listing_view = jet_engine()->listings->data->get_listing_type( $listing_id );
			$content      = apply_filters( 'jet-engine/listing/content/' . $listing_view, null, $listing_id );

			return $content;
		}

		public function add_listing_link_to_content( $content, $settings ) {

			if ( empty( $settings ) || empty( $settings['listing_link'] ) ) {
				return $content;
			}

			$url = apply_filters_deprecated(
				'jet-engine/elementor-views/frontend/custom-listing-url',
				array( false, $settings ),
				'2.1.5',
				'jet-engine/listings/frontend/custom-listing-url'
			);

			$url = apply_filters(
				'jet-engine/listings/frontend/custom-listing-url',
				$url,
				$settings
			);

			if ( ! $url ) {
				$source = ! empty( $settings['listing_link_source'] ) ? $settings['listing_link_source'] : '_permalink';

				switch ( $source ) {
					case '_permalink':
						$url = jet_engine()->listings->data->get_current_object_permalink();
						break;
					case 'options_page':
						$option = ! empty( $settings['listing_link_option'] ) ? $settings['listing_link_option'] : false;
						$url    = jet_engine()->listings->data->get_option( $option );
						break;
					case 'object_prop':
						$prop = ! empty( $settings['listing_link_object_prop'] ) ? $settings['listing_link_object_prop'] : false;
						$url = jet_engine()->listings->data->get_prop( $prop );
						break;
					default:
						$url = jet_engine()->listings->data->get_meta( $source );
				}
			}

			$custom_url = ! empty( $settings['listing_link_custom_url'] ) ? $settings['listing_link_custom_url'] : '';

			if ( ! empty( $custom_url ) ) {
				$custom_url = do_shortcode( $custom_url );
				$custom_url = jet_engine()->listings->macros->do_macros( $custom_url );

				$url = $custom_url;
			}

			$url = \Jet_Engine_Tools::add_query_args_by_settings(
				$url,
				array(
					'add_query_args' => $settings['listing_link_add_query_args'] ?? '',
					'query_args'    => $settings['listing_link_query_args'] ?? '',
				)
			);

			$prefix = isset( $settings['listing_link_prefix'] ) ? $settings['listing_link_prefix'] : '';

			if ( $prefix ) {
				$url = $prefix . $url;
			}

			if ( ! empty( $settings['listing_link_url_anchor'] ) ) {
				$url_anchor = $settings['listing_link_url_anchor'];
				$url_anchor = do_shortcode( $url_anchor );
				$url_anchor = jet_engine()->listings->macros->do_macros( $url_anchor );

				$url = $url . '#' . esc_attr( $url_anchor );
			}

			$overlay_attrs = array(
				'class'    => 'jet-engine-listing-overlay-wrap',
				'data-url' => $url,
			);

			$link_attrs = array(
				'href'  => $url,
				'class' => 'jet-engine-listing-overlay-link',
			);

			$open_in_new = isset( $settings['listing_link_open_in_new'] ) ? $settings['listing_link_open_in_new'] : '';
			$rel_attr    = isset( $settings['listing_link_rel_attr'] ) ? $settings['listing_link_rel_attr'] : '';
			$aria_label  = isset( $settings['listing_link_aria_label'] ) ? $settings['listing_link_aria_label'] : '';
			$link_text   = '';

			if ( $open_in_new ) {
				$overlay_attrs['data-target'] = '_blank';
				$link_attrs['target']         = '_blank';
			}

			if ( $rel_attr ) {
				$link_attrs['rel'] = $rel_attr;
			}

			if ( $aria_label ) {
				$link_attrs['aria-label'] = esc_attr( $aria_label );
				$link_text = esc_html( $aria_label );
			}

			$overlay_attrs = apply_filters( 'jet-engine/listings/frontend/listing-link/overlay-attrs', $overlay_attrs, $settings );
			$link_attrs    = apply_filters( 'jet-engine/listings/frontend/listing-link/link-attrs', $link_attrs, $settings );

			$link = sprintf( '<a %s>%s</a>', Jet_Engine_Tools::get_attr_string( $link_attrs ), $link_text );

			return sprintf(
				'<div %3$s>%1$s%2$s</div>',
				$content,
				$link,
				Jet_Engine_Tools::get_attr_string( $overlay_attrs )
			);
		}

		/**
		 * Setup data
		 *
		 * @param $post_obj
		 */
		public function setup_data( $post_obj = null ) {

			if ( $post_obj && 'WP_Post' === get_class( $post_obj ) ) {
				global $post;
				$post = $post_obj;
				setup_postdata( $post );
			}

			do_action( 'jet-engine/listings/frontend/setup-data', $post_obj, $this );

			jet_engine()->listings->data->set_current_object( $post_obj );

		}

		/**
		 * Reset data
		 *
		 * @return void
		 */
		public function reset_data() {

			do_action( 'jet-engine/listings/frontend/reset-data', jet_engine()->listings->data, $this );

			if ( 'posts' === jet_engine()->listings->data->get_listing_source() ) {
				wp_reset_postdata();
			}

			//jet_engine()->listings->data->reset_current_object();

		}

		/**
		 * Get custom action url.
		 *
		 * @param string $action
		 * @param array  $args
		 *
		 * @return string
		 */
		public function get_custom_action_url( $action = '', $args = array() ) {
			$default_args = array(
				'action' => $action,
				'event'  => 'click',
			);

			$query_args = array_merge( $default_args, $args );

			return sprintf( '#jet-engine-action&%s', build_query( $query_args ) );
		}

	}

}
