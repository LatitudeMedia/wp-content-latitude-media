<?php
namespace ElementorPro\Modules\AtomicForm\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Webhook_Action extends Action_Base {

	public function get_type(): string {
		return Action_Type::WEBHOOK;
	}

	public function execute( array $form_data, array $widget_settings, array $context ): array {
		$validation = $this->validate_settings( $widget_settings );
		if ( is_wp_error( $validation ) ) {
			return $this->failure( $validation->get_error_message() );
		}

		$url = $widget_settings['webhook_url'];
		$method = strtoupper( $widget_settings['webhook_method'] ?? 'POST' );
		$timeout = isset( $widget_settings['webhook_timeout'] ) ? absint( $widget_settings['webhook_timeout'] ) : 30;

		$payload = [
			'formData' => $form_data,
			'postId' => $context['post_id'],
			'formId' => $context['form_id'],
			'formName' => $context['form_name'],
			'timestamp' => current_time( 'mysql' ),
			'siteUrl' => get_site_url(),
		];

		/**
		 * Filter webhook payload for atomic forms.
		 *
		 * @param array $payload Webhook payload.
		 * @param array $form_data Form data.
		 * @param array $widget_settings Widget settings.
		 * @param array $context Form context.
		 */
		$payload = apply_filters(
			'elementor_pro/atomic_forms/webhook_payload',
			$payload,
			$form_data,
			$widget_settings,
			$context
		);

		$args = [
			'method' => $method,
			'timeout' => $timeout,
			'headers' => [
				'Content-Type' => 'application/json',
				'User-Agent' => 'Elementor Pro Atomic Forms/' . ELEMENTOR_PRO_VERSION,
			],
			'body' => wp_json_encode( $payload ),
		];

		if ( ! empty( $widget_settings['webhook_headers'] ) && is_array( $widget_settings['webhook_headers'] ) ) {
			$args['headers'] = array_merge( $args['headers'], $widget_settings['webhook_headers'] );
		}

		$response = wp_remote_request( $url, $args );

		if ( is_wp_error( $response ) ) {
			return $this->failure(
				sprintf(
					/* translators: %s: Error message. */
					__( 'Webhook request failed: %s', 'elementor-pro' ),
					$response->get_error_message()
				)
			);
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( $response_code >= 200 && $response_code < 300 ) {
			return $this->success(
				__( 'Webhook delivered successfully', 'elementor-pro' ),
				[
					'responseCode' => $response_code,
					'responseBody' => $response_body,
				]
			);
		}

		return $this->failure(
			sprintf(
				/* translators: %d: HTTP status code. */
				__( 'Webhook returned error status code: %d', 'elementor-pro' ),
				$response_code
			),
			[
				'responseCode' => $response_code,
				'responseBody' => $response_body,
			]
		);
	}

	protected function validate_settings( array $widget_settings ) {
		if ( empty( $widget_settings['webhook_url'] ) ) {
			return new \WP_Error(
				'missing_url',
				__( 'Webhook URL is required', 'elementor-pro' )
			);
		}

		if ( ! filter_var( $widget_settings['webhook_url'], FILTER_VALIDATE_URL ) ) {
			return new \WP_Error(
				'invalid_url',
				__( 'Invalid webhook URL', 'elementor-pro' )
			);
		}

		if ( isset( $widget_settings['webhook_method'] ) ) {
			$allowed_methods = [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE' ];
			if ( ! in_array( strtoupper( $widget_settings['webhook_method'] ), $allowed_methods, true ) ) {
				return new \WP_Error(
					'invalid_method',
					__( 'Invalid HTTP method', 'elementor-pro' )
				);
			}
		}

		return true;
	}
}
