<?php
namespace Jet_Engine\Modules\Calendar\Blocks_Views;

class Manager {

	public function __construct() {
		add_action( 'jet-engine/blocks-views/register-block-types', [ $this, 'register_blocks' ] );
		add_action( 'jet-engine/blocks-views/editor-script/after',  [ $this, 'editor_assets' ] );
		add_filter( 'jet-engine/blocks-views/editor/config',        [ $this, 'add_editor_config' ] );
	}

	/**
	 * Register block types
	 *
	 * @param  object $blocks_types
	 * @return void
	 */
	public function register_blocks( $blocks_types ) {

		require jet_engine()->modules->modules_path( 'calendar/blocks-views/calendar-block.php' );
		require jet_engine()->modules->modules_path( 'calendar/blocks-views/multiday-calendar-block.php' );

		$calendar_type = new \Jet_Listing_Calendar_Block_Type();
		$multiday_calendar_type = new \Jet_Listing_Multiday_Calendar_Block_Type();

		$blocks_types->register_block_type( $calendar_type );
		$blocks_types->register_block_type( $multiday_calendar_type );
	}

	/**
	 * Enqueue editor assets
	 *
	 * @return void
	 */
	public function editor_assets() {
		wp_enqueue_script(
			'jet-engine-calendar-blocks-views',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/js/blocks-views.js' ),
			array( 'jet-engine-blocks-views' ),
			jet_engine()->get_version(),
			true
		);
	}

	/**
	 * Add editor config.
	 *
	 * @param  array $config
	 * @return array
	 */
	public function add_editor_config( $config = array() ) {

		$config['atts']['listingCalendar'] = jet_engine()->blocks_views->block_types->get_block_atts( 'listing-calendar' );
		$config['atts']['listingMultidayCalendar'] = jet_engine()->blocks_views->block_types->get_block_atts( 'listing-multiday-calendar' );

		return $config;
	}

}
