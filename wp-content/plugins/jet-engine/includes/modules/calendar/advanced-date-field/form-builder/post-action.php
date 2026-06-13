<?php
namespace Jet_Engine\Dynamic_Calendar\Advanced_Date_Field;

/**
 * Allows to save advanced date into the post meta in the way compatible with JetEngine post meta
 */
class Post_Action {

	protected $match_fields = [];

	public function __construct() {
		add_action(
			'jet-form-builder/modifier/before-run',
			array( $this, 'add_advanced_date_meta_config' )
		);

		add_action(
			'jet-form-builder/action/after-post-insert',
			array( $this, 'adjust_advanced_date_meta' ),
			10, 3
		);

		add_action(
			'jet-form-builder/action/after-post-update',
			array( $this, 'adjust_advanced_date_meta' ),
			10, 3
		);
	}

	/**
	 * Iterate through modifier fields map and compare each with request.
	 * If we have %field%__config for the field - we need to add it into the fields map as a separate key
	 * So it will be saved as the separate meta field on the next step.
	 *
	 * @param object $modifier
	 * @return void
	 */
	public function add_advanced_date_meta_config( $modifier ) {

		$initial_fields_map = $modifier->fields_map;
		$request            = $modifier->get_request();

		foreach ( $initial_fields_map as $field => $prop ) {

			$config_field = $field . '__config';

			if ( ! isset( $request[ $config_field ] ) || isset( $initial_fields_map[ $config_field ] ) ) {
				continue;
			}

			$modifier->fields_map[ $config_field ] = $prop . '__config';
			unset( $modifier->fields_map[ $field ] );

			$this->match_fields[ $prop ] = isset( $request[ $field ] ) ? $request[ $field ] : false;
		}
	}

	/**
	 * Make sure the meta data is stored in the JetEngine compatible way
	 *
	 * @param object $form_action
	 * @param object $handler
	 * @param object $post_action
	 * @return void
	 */
	public function adjust_advanced_date_meta( $form_action, $handler, $post_action ) {
		if ( ! empty( $this->match_fields ) ) {
			foreach ( $this->match_fields as $field => $values ) {
				$this->adjust_single_field( $field, $values, $post_action );
			}
		}
	}

	/**
	 * Processes the post-action for an advanced date field.
	 *
	 * @param mixed  $field       The field data.
	 * @param mixed  $values      The value of the field.
	 * @param object $post_action A parameter used to modify or adjust the field's behavior during processing.
	 *
	 * @return void
	 */
	public function adjust_single_field( $field, $values, $post_action ) {

		delete_post_meta( $post_action->get_inserted_id(), $field );
		delete_post_meta( $post_action->get_inserted_id(), $field . '__end_date' );

		if ( ! empty( $values ) && is_array( $values ) ) {
			foreach ( $values as $value ) {
				if ( is_array( $value ) ) {

					$start_date = isset( $value['start'] ) ? $value['start'] : false;
					$end_date   = isset( $value['end'] ) ? $value['end'] : false;

					if ( $start_date ) {
						add_post_meta( $post_action->get_inserted_id(), $field, $start_date );
					}

					if ( $end_date ) {
						add_post_meta(
							$post_action->get_inserted_id(),
							$field . '__end_date',
							$end_date
						);
					}
				} else {
					add_post_meta( $post_action->get_inserted_id(), $field, $value );
				}
			}
		}
	}
}