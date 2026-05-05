<?php
namespace Jet_Engine\Blocks_Views;

use Jet_Engine_Sanitizer;

/**
 * Make core blocks styles compatible with ajax-loaded listings.
 *
 * @see https://github.com/Crocoblock/issues-tracker/issues/15245
 */
class Core_Styles {
	public function __construct() {
		add_action(
			'jet-engine/listing/grid/after-loop',
			[ $this, 'get_compiled_styles' ]
		);
	}

	/**
	 * Check if is AJAX request and get styles compiled for the content rendered during this request.
	 * Than we need to prefix them with listing-specific class to avoid conflicts on the front-end.
	 *
	 * @param object $listing_renderer
	 */
	public function get_compiled_styles( $listing_renderer ) {

		if (
			'blocks' === $listing_renderer->view
			&& ( jet_engine()->listings->is_listing_ajax()
				|| $this->is_filters_request()
				|| ( defined( 'REST_REQUEST' ) && REST_REQUEST )
				|| ( defined( 'DOING_AJAX' ) && DOING_AJAX ) // maybe listing grid is loaded with 3rd part AJAX request
			)
		) {
			wp_enqueue_stored_styles();
			$core_styles = wp_styles()->get_data( 'core-block-supports', 'after' );
			$variations_styles = wp_styles()->get_data( 'block-style-variation-styles', 'after' );
			$listing_styles = '';

			if ( ! empty( $variations_styles ) ) {
				foreach ( $variations_styles as $style ) {
					$listing_styles .= str_replace(
						':root',
						'.jet-listing-grid--' . $listing_renderer->listing_id,
						$style
					 );
				}
			}

			if ( ! empty( $core_styles ) ) {

				foreach ( $core_styles as $style ) {
					$listing_styles .= str_replace(
						'.wp-',
						'.jet-listing-grid--' . $listing_renderer->listing_id . ' .wp-',
						$style
					 );
				}

				printf(
					'<style type="text/css" id="jet-listing-grid-%s-styles">%s</style>',
					absint( $listing_renderer->listing_id ),
					Jet_Engine_Sanitizer::sanitize_inline_css( $listing_styles ) // phpcs:ignore
				);
			}
		}
	}

	/**
	 * Check if current request is JetSmartFilters AJAX request.
	 *
	 * @return boolean
	 */
	public function is_filters_request() {

		if ( function_exists( 'jet_smart_filters' ) && jet_smart_filters()->query->is_ajax_filter() ) {
			return true;
		}

		return false;
	}
}
