<?php
namespace Jet_Engine\Dynamic_Calendar\Advanced_Date_Field;

/**
 * Allows to get advanced date config instead of the raw date value for the advanced date field
 */
class Post_Preset {

	public function __construct() {
		add_action(
			'jet-form-builder/preset/source/value',
			array( $this, 'get_advanced_date_meta' ),
			10, 2
		);
	}

	/**
	 * Get advanced date on preset value retirieving
	 */
	public function get_advanced_date_meta( $result, $preset_source ) {

		$field_data = $preset_source->get_field_data();

		if ( ! empty( $field_data )
			&& isset( $field_data['prop'] )
			&& 'post_meta' === $field_data['prop'] ) {
			$field_name = ! empty( $field_data['key'] ) ? $field_data['key'] . '__config' : false;
			if ( $field_name ) {

				// We can't get the field type at this point so always need to check if config value is exists.
				$config = get_post_meta( $preset_source->src()->ID, $field_name, true );

				if ( ! empty( $config ) ) {
					$result = $config;
				}
			}
		}

		return $result;
	}
}