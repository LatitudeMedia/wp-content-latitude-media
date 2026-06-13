<?php
namespace Jet_Engine\Modules\Calendar\Elementor_Views;

class Manager {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action(
			'jet-engine/elementor-views/widgets/register',
			array( $this, 'register_calendar_widgets' ), 20, 2
		);

		add_action(
			'elementor/preview/enqueue_scripts',
			array( $this, 'preview_assets' )
		);
	}

	public function preview_assets() {
		wp_enqueue_style(
			'jet-engine-multiday-calendar',
			jet_engine()->plugin_url( 'includes/modules/calendar/assets/css/multiday-calendar.css' ),
			array(),
			jet_engine()->get_version()
		);
	}

	/**
	 * Register calendar widget
	 *
	 * @param $widgets_manager
	 * @param $elementor_views
	 *
	 * @return void
	 */
	public function register_calendar_widgets( $widgets_manager, $elementor_views ) {

		$elementor_views->register_widget(
			jet_engine()->modules->modules_path( 'calendar/elementor-views/calendar-widget.php' ),
			$widgets_manager,
			'Elementor\Jet_Listing_Calendar_Widget'
		);

		$elementor_views->register_widget(
			jet_engine()->modules->modules_path( 'calendar/elementor-views/multiday-calendar-widget.php' ),
			$widgets_manager,
			'Jet_Engine\Modules\Calendar\Elementor_Views\Multiday_Calendar_Widget'
		);
	}
}
