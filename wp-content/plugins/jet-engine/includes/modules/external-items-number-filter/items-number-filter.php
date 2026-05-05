<?php
/**
 * Custom content types module
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Jet_Engine_Module_Dynamic_Tables class
 */
class Jet_Engine_Module_Items_Number_Filter extends Jet_Engine_External_Module_Base {

	/**
	 * Module ID
	 *
	 * @return string
	 */
	public function module_id() {
		return 'jet-engine-items-number-filter';
	}

	/**
	 * Check if related plugin for current external module is active
	 *
	 * @return boolean [description]
	 */
	public function is_related_plugin_active() {
		return defined( '\JET_ENGINE_ITEMS_NUMBER_FILTER_VERSION' );
	}

	/**
	 * Module name
	 *
	 * @return string
	 */
	public function module_name() {
		return __( 'Items Number Filter', 'jet-engine' );
	}

	/**
	 * Returns detailed information about current module for the dashboard page
	 * @return [type] [description]
	 */
	public function get_module_description() {
		return '<p>Requires the <strong>JetSmartFilters plugin</strong>.<br>Allows users to add a filter, that sets the number of items per page depending on screen width.</p>';
	}

	/**
	 * Returns information about the related plugin for current module
	 *
	 * @return [type] [description]
	 */
	public function get_related_plugin_data() {
		return array(
			'file' => 'jet-engine-items-number-filter/jet-engine-items-number-filter.php',
			'name' => 'JetEngine - Items Number Filter',
		);
	}

	/**
	 * Returns actions allowed after plugin installation
	 *
	 * @return [type] [description]
	 */
	public function get_installed_actions() {
		return array();
	}

	public function get_video_embed() {
		return '';
	}

	/**
	 * Returns array links to the module-related resources
	 * @return array
	 */
	public function get_module_links() {
		return array();
	}

	/**
	 * Module init
	 *
	 * @return void
	 */
	public function module_init() {}

}
