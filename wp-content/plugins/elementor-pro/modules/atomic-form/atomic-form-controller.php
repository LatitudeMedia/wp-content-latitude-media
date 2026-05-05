<?php
namespace ElementorPro\Modules\AtomicForm;

use Elementor\Modules\AtomicWidgets\DynamicTags\Dynamic_Prop_Type;
use Elementor\Utils as ElementorUtils;
use ElementorPro\Modules\AtomicForm\Actions\Action_Runner;
use ElementorPro\Modules\AtomicWidgets\Settings_Resolver;
use ElementorPro\Modules\Forms\Classes\Ajax_Handler;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Atomic_Form_Controller {
	const NONCE_ACTION = 'elementor_pro_atomic_forms_send_form';

	public static function is_form_submitted(): bool {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is validated in ajax_send_form.
		return wp_doing_ajax()
			&& 'elementor_pro_atomic_forms_send_form' === ElementorUtils::get_super_global_value( $_POST, 'action' );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
	}

	public function ajax_send_form(): void {

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is validated below.
		$post_data = [
			'_nonce' => ElementorUtils::get_super_global_value( $_POST, '_nonce' ),
			'post_id' => ElementorUtils::get_super_global_value( $_POST, 'post_id' ),
			'form_id' => ElementorUtils::get_super_global_value( $_POST, 'form_id' ),
			'form_name' => ElementorUtils::get_super_global_value( $_POST, 'form_name' ),
			'form_fields' => ElementorUtils::get_super_global_value( $_POST, 'form_fields' ) ?? [],
		];
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( ! $this->is_nonce_valid( $post_data ) ) {
			$this->send_invalid_form_response();
		}

		$post_id = absint( $post_data['post_id'] ?? 0 );
		$form_id = sanitize_text_field( $post_data['form_id'] ?? '' );
		$form_fields = $post_data['form_fields'] ?? [];

		if ( ! $post_id || ! $form_id || empty( $form_fields ) ) {
			$this->send_invalid_form_response();
		}

		$form_data = $this->convert_form_fields_to_data( $form_fields );

		if ( empty( $form_data ) ) {
			$this->send_invalid_form_response();
		}

		$field_metadata = $this->extract_field_metadata( $form_fields );

		$widget_settings = $this->get_widget_settings( $post_id, $form_id );

		if ( is_wp_error( $widget_settings ) ) {
			$this->send_error_response( $widget_settings->get_error_message() );
		}

		$posted_form_name = sanitize_text_field( $post_data['form_name'] ?? '' );
		$form_name = $this->resolve_form_name( $posted_form_name, $form_id );

		$spam_check = apply_filters(
			'elementor_pro/atomic_forms/spam_check',
			false,
			$form_fields,
			$widget_settings,
			$post_id
		);

		if ( $spam_check ) {
			$this->send_error_response(
				__( 'Your submission was flagged as spam. Please try again or contact the site administrator.', 'elementor-pro' )
			);
		}

		$actions = $widget_settings['actions-after-submit'] ?? [];

		if ( empty( $actions ) ) {
			$this->send_error_response( __( 'No actions configured for this form', 'elementor-pro' ) );
		}

		$results = Action_Runner::execute_actions(
			$actions,
			$form_data,
			$widget_settings,
			[
				'post_id' => $post_id,
				'form_id' => $form_id,
				'form_name' => $form_name,
				'field_metadata' => $field_metadata,
			]
		);

		$this->send_response(
			$results['actionResults'],
			$results['allActionsSucceeded'],
			$results['failedActions']
		);
	}

	private function is_nonce_valid( array $post_data ): bool {
		$nonce = $post_data['_nonce'] ?? '';

		if ( ! $nonce ) {
			return false;
		}

		return wp_verify_nonce( $nonce, self::NONCE_ACTION );
	}

	private function convert_form_fields_to_data( array $form_fields ): array {
		$form_data = [];

		foreach ( $form_fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$id = sanitize_text_field( $field['id'] ?? '' );
			$value = $field['value'] ?? '';

			if ( ! $id ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$form_data[ $id ] = array_map( 'sanitize_text_field', $value );
			} else {
				$type = sanitize_text_field( $field['type'] ?? 'text' );

				if ( 'textarea' === $type ) {
					$form_data[ $id ] = sanitize_textarea_field( $value );
				} else {
					$form_data[ $id ] = sanitize_text_field( $value );
				}
			}
		}

		return $form_data;
	}

	private function resolve_form_name( string $posted_form_name, string $form_id ): string {
		return ! empty( $posted_form_name ) ? $posted_form_name : $form_id;
	}

	private function extract_field_metadata( array $form_fields ): array {
		$metadata = [];

		foreach ( $form_fields as $field ) {
			if ( ! is_array( $field ) ) {
				continue;
			}

			$id = sanitize_text_field( $field['id'] ?? '' );

			if ( ! $id ) {
				continue;
			}

			$metadata[ $id ] = [
				'label' => sanitize_text_field( $field['label'] ?? '' ),
				'type' => sanitize_text_field( $field['type'] ?? '' ),
			];
		}

		return $metadata;
	}

	private function get_widget_settings( int $post_id, string $form_id ) {
		$document = Plugin::elementor()->documents->get( $post_id );

		if ( ! $document ) {
			return new \WP_Error(
				'document_not_found',
				__( 'Document not found', 'elementor-pro' )
			);
		}

		$element_data = $document->get_elements_data();
		$form_element = ElementorUtils::find_element_recursive( $element_data, $form_id );

		if ( empty( $form_element ) ) {
			return new \WP_Error(
				'form_not_found',
				__( 'Form element not found', 'elementor-pro' )
			);
		}

		$settings = $form_element['settings'] ?? [];

		$settings = $this->resolve_dynamic_tags_in_settings( $settings, $post_id );

		$resolved = Settings_Resolver::resolve( $settings );

		if ( ! isset( $resolved['actions-after-submit'] ) && isset( $resolved['email'] ) ) {
			$resolved['actions-after-submit'] = [ 'email' ];
		}

		return $resolved;
	}

	/**
	 * @param array|string $value
	 * @param int $post_id
	 * @return array|string|null
	 */
	private function resolve_dynamic_tags_in_settings( $value, int $post_id ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		if ( Dynamic_Prop_Type::is_dynamic_prop_value( $value ) ) {
			if ( ! empty( $value['disabled'] ) ) {
				return null;
			}

			$tag_data = $value['value'] ?? [];
			$tag_name = $tag_data['name'] ?? '';
			$tag_settings = $tag_data['settings'] ?? [];

			if ( empty( $tag_name ) ) {
				return null;
			}

			Plugin::elementor()->db->switch_to_post( $post_id );

			try {
				return Plugin::elementor()->dynamic_tags->get_tag_data_content(
					null,
					$tag_name,
					$tag_settings
				);
			} finally {
				Plugin::elementor()->db->restore_current_post();
			}
		}

		return array_map( function ( $item ) use ( $post_id ) {
			return $this->resolve_dynamic_tags_in_settings( $item, $post_id );
		}, $value );
	}

	private function send_invalid_form_response(): void {
		wp_send_json_error( [
			'message' => Ajax_Handler::get_default_message( Ajax_Handler::INVALID_FORM, [] ),
		] );
	}

	private function send_error_response( string $message = '' ): void {
		wp_send_json_error( [
			'message' => $message ?? Ajax_Handler::get_default_message( Ajax_Handler::ERROR, [] ),
		] );
	}

	private function send_response( array $action_results, bool $all_actions_succeeded, array $failed_actions ): void {
		$response_data = [
			'actionResults' => $action_results,
			'allActionsSucceeded' => $all_actions_succeeded,
			'failedActions' => $failed_actions,
		];

		if ( $all_actions_succeeded ) {
			wp_send_json_success( [
				'message' => Ajax_Handler::get_default_message( Ajax_Handler::SUCCESS, [] ),
				'data' => $response_data,
			] );
		} else {
			$has_success = ! empty( $action_results ) && count( $failed_actions ) < count( $action_results );

			if ( $has_success ) {
				wp_send_json_success( [
					'message' => Ajax_Handler::get_default_message( Ajax_Handler::SUCCESS, [] ),
					'data' => $response_data,
				] );
			} else {
				wp_send_json_error( [
					'message' => Ajax_Handler::get_default_message( Ajax_Handler::ERROR, [] ),
					'data' => $response_data,
				] );
			}
		}
	}

	public function __construct() {
		add_action( 'wp_ajax_elementor_pro_atomic_forms_send_form', [ $this, 'ajax_send_form' ] );
		add_action( 'wp_ajax_nopriv_elementor_pro_atomic_forms_send_form', [ $this, 'ajax_send_form' ] );
	}
}
