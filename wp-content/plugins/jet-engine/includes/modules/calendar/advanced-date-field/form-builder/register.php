<?php
namespace Jet_Engine\Dynamic_Calendar\Advanced_Date_Field;

/**
 * Register Advanced Date field for JetFormBuilder
 */
class Form_Builder {

	protected $base_path;
	protected $base_url;

	public function __construct( $base_path, $base_url ) {

		if ( ! defined( 'JET_FORM_BUILDER_VERSION' ) ) {
			return;
		}

		$this->base_path = $base_path;
		$this->base_url  = $base_url;

		require_once $base_path . 'block.php';
		require_once $base_path . 'block-render.php';
		require_once $base_path . 'post-action.php';
		require_once $base_path . 'post-preset.php';

		new Post_Action();
		new Post_Preset();

		add_action(
			'jet-form-builder/blocks/register',
			array( $this, 'register_block' )
		);

		add_filter(
			'jet-form-builder/request-handler/before-init',
			array( $this, 'register_field_parser' )
		);
	}

	/**
	 * Register field parser
	 *
	 * @param  object $parsers Registered parsers list.
	 * @return array
	 */
	public function register_field_parser( $parsers ) {
		require_once $this->base_path . 'field-parser.php';
		$parsers->rep_install_item( new Field_Parser() );
	}

	/**
	 * Register block
	 *
	 * @return void
	 */
	public function register_block( $manager ) {

		add_action(
			'jet-form-builder/editor-assets/before',
			array( $this, 'before_init_editor_assets' )
		);

		$manager->register_block_type( new Block( $this->base_path, $this->base_url ) );
	}

	/**
	 * Enqueue block editor script
	 *
	 * @return void
	 */
	public function before_init_editor_assets() {

		wp_enqueue_script(
			'jet-engine-advanced-field-editor',
			$this->base_url . 'js/jet-form-builder-editor.js',
			array(),
			jet_engine()->get_version(),
			true
		);

		wp_localize_script(
			'jet-engine-advanced-field-editor',
			'JetEngineAdvancedFieldData', array(
				'week_days' => $this->get_localized_weekdays(),
			)
		);
	}

	/**
	 * Get localized weekdays
	 *
	 * @return array
	 */
	public function get_localized_weekdays() {

		global $wp_locale;

		for ( $i = 0; $i < 7; $i++ ) {
			$week_days[] = $wp_locale->get_weekday( $i );
		}

		foreach ( $week_days as $key => $day ) {
			$week_days[ $key ] = $wp_locale->get_weekday_abbrev( $day );
		}

		return array_values( $week_days );
	}
}
