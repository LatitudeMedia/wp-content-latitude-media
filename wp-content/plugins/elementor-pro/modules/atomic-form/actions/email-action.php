<?php
namespace ElementorPro\Modules\AtomicForm\Actions;

use ElementorPro\Modules\AtomicForm\Actions\Email_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Email_Action extends Action_Base {

	public function get_type(): string {
		return Action_Type::EMAIL;
	}

	public function execute( array $form_data, array $widget_settings, array $context ): array {
		$validation = $this->validate_settings( $widget_settings );

		if ( is_wp_error( $validation ) ) {
			return $this->failure( $validation->get_error_message() );
		}

		$email_settings = new Email_Settings( $widget_settings );

		$to = $email_settings->to();
		$from = $email_settings->from();
		$from_name = $email_settings->from_name();
		$message  = $email_settings->message();
		$subject = $email_settings->subject();
		$reply_to = $email_settings->reply_to();
		$cc = $email_settings->cc();
		$bcc = $email_settings->bcc();
		$content_type = $email_settings->content_type();

		$field_metadata = $context['field_metadata'] ?? [];
		$message = $this->replace_shortcodes( $message, $form_data, 'html' === $content_type, $field_metadata );

		$headers = [];
		$headers[] = sprintf( 'From: %s <%s>', $from_name, $from );
		$headers[] = sprintf( 'Reply-To: %s', $reply_to );

		if ( 'html' === $content_type ) {
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
		}

		if ( ! empty( $cc ) ) {
			$headers[] = sprintf( 'Cc: %s', $cc );
		}

		if ( ! empty( $bcc ) ) {
			$headers[] = sprintf( 'Bcc: %s', $bcc );
		}

		/**
		 * Filter email headers for atomic forms.
		 *
		 * @param array $headers Email headers.
		 * @param array $form_data Form data.
		 * @param array $widget_settings Widget settings.
		 */
		$headers = apply_filters( 'elementor_pro/atomic_forms/email_headers', $headers, $form_data, $widget_settings );

		/**
		 * Filter email message for atomic forms.
		 *
		 * @param string $message Email message.
		 * @param array $form_data Form data.
		 * @param array $widget_settings Widget settings.
		 */
		$message = apply_filters( 'elementor_pro/atomic_forms/email_message', $message, $form_data, $widget_settings );

		$email_sent = wp_mail( $to, $subject, $message, $headers );

		if ( ! $email_sent ) {
			return $this->failure( __( 'Failed to send email', 'elementor-pro' ) );
		}

		return $this->success( __( 'Email sent successfully', 'elementor-pro' ) );
	}

	protected function validate_settings( array $widget_settings ) {
		$email_settings = new Email_Settings( $widget_settings );
		$email_to = $email_settings->to();

		if ( ! empty( $email_to ) && ! is_email( $email_to ) ) {
			$emails = array_map( 'trim', explode( ',', $email_to ) );
			foreach ( $emails as $email ) {
				if ( ! is_email( $email ) ) {
					return new \WP_Error(
						'invalid_email',
						sprintf(
							/* translators: %s: Invalid email address. */
							__( 'Invalid email address: %s', 'elementor-pro' ),
							$email
						)
					);
				}
			}
		}

		return true;
	}

	private function replace_shortcodes( string $message, array $form_data, bool $is_html, array $field_metadata = [] ): string {
		$line_break = $is_html ? '<br>' : "\n";

		if ( strpos( $message, '[all-fields]' ) !== false ) {
			$all_fields_text = '';

			foreach ( $form_data as $key => $value ) {
				$meta = $field_metadata[ $key ] ?? [];
				$formatted_key = ! empty( $meta['label'] ) ? $meta['label'] : ucwords( str_replace( [ '_', '-' ], ' ', $key ) );
				$formatted_value = is_array( $value ) ? implode( ', ', $value ) : $value;

				if ( $is_html ) {
					$formatted_key = esc_html( $formatted_key );

					if ( is_string( $formatted_value ) ) {
						$formatted_value = nl2br( esc_html( $formatted_value ) );
					}
				}

				$all_fields_text .= sprintf(
					'%s: %s%s',
					$formatted_key,
					$formatted_value,
					$line_break
				);
			}

			$message = str_replace( '[all-fields]', $all_fields_text, $message );
		}

		$message = preg_replace_callback(
			'/\[field[^\]]*id=["\']([^"\']+)["\'][^\]]*\]/',
			function ( $matches ) use ( $form_data ) {
				$field_id = $matches[1];

				if ( isset( $form_data[ $field_id ] ) ) {
					$value = $form_data[ $field_id ];

					return is_array( $value ) ? implode( ', ', $value ) : $value;
				}

				return '';
			},
			$message
		);

		return $message;
	}
}
