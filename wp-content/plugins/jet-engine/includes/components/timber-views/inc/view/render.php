<?php
/**
 * Timber editor render class
 */
namespace Jet_Engine\Timber_Views\View;

use Jet_Engine\Timber_Views\Package;
use Timber\Timber;
use Timber\Post;
use Timber\Loader;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Render {

	private $rendered_css = [];
	private $hidden_listings = [];
	private $twig = null;

	public function __construct() {
		add_filter( 'jet-engine/listing/content/twig', array( $this, 'get_listing_content' ), 10, 2 );
		add_action( 'jet-engine/listing/on-hide', array( $this, 'set_is_hidden' ) );
	}

	public function get_listing_content( $content, $listing_id ) {

		jet_engine()->listings->ensure_listing_doc_class();

		$html = \Jet_Engine_Listings_Document::get_listing_html_by_id( $listing_id );
		$current_object = jet_engine()->listings->data->get_current_object();

		if ( ! $this->twig ) {
			$dummy_loader = new Loader();
			$this->twig = $dummy_loader->get_twig();
		}

		try {
			$content = $this->get_listing_css( $listing_id ) . Package::instance()->render_html(
				$html,
				Package::instance()->get_context_for_object( $current_object ),
				$this->twig
			);
		} catch ( \Exception $e ) {
			$content = 'Error while rendering listing: ' . $e->getMessage();
		}

		return $content;
	}

	public function get_listing_css( $listing_id ) {

		if ( in_array( $listing_id, $this->rendered_css ) ) {
			return;
		}

		$force_css_render = in_array( $listing_id, $this->hidden_listings ) ? true : false;
		$force_css_render = apply_filters(
			'jet-engine/twig-views/force-render-css',
			$force_css_render,
			$listing_id
		);

		/**
		 * common hook shared across different views
		 */
		$force_css_render = apply_filters(
			'jet-engine/listing/force-render-css',
			$force_css_render,
			$listing_id
		);

		if ( ! $force_css_render ) {
			$this->rendered_css[] = $listing_id;
		}

		return sprintf(
			'<style>%s</style>',
			str_replace( 'selector', '.jet-listing-grid--' . $listing_id, \Jet_Engine_Listings_Document::get_listing_css_by_id( $listing_id )
		) );
	}

	/**
	 * Mark listing as hidden by dynamic visibility to force it to
	 * try render it's own CSS on each next attempt.
	 */
	public function set_is_hidden( $listing_id ) {

		if ( ! in_array( $listing_id, $this->hidden_listings ) ) {
			$this->hidden_listings[] = $listing_id;
		}

		if ( ! in_array( $listing_id, $this->rendered_css ) ) {
			return;
		}

		$this->rendered_css = array_diff( $this->rendered_css, [ $listing_id ] );
	}

}
