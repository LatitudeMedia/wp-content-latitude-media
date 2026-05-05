<?php
namespace Crocoblock\Agent_UI;

class Storage {

	/**
	 * Singleton instance of the storage class.
	 *
	 * @var Storage|null
	 */
	private static $instance = null;

	/**
	 * Name of the storage for the API key.
	 *
	 * @var string
	 */
	private const API_KEY_STORAGE = 'crocoblock_agent_ui_key';

	/**
	 * Name of the storage for the models.
	 *
	 * @var string
	 */
	private const MODELS_STORAGE = 'crocoblock_agent_models';

	/**
	 * Name of the storage for the selected model.
	 *
	 * @var string
	 */
	private const SELECTED_MODEL = 'crocoblock_agent_selected_model';

	/**
	 * Get the singleton instance of the storage class.
	 *
	 * @return Storage
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Store AI API key in the WordPress options table.
	 *
	 * @param string $key
	 * @return void
	 */
	public function set_key( $key = '' ) {
		if ( ! $key ) {
			delete_option( self::API_KEY_STORAGE );
		} else {
			update_option( self::API_KEY_STORAGE, $this->encrypt_string( $key ) );
		}
	}

	/**
	 * Encrypt a string using a secure method.
	 *
	 * @param string $string
	 * @return string|null
	 */
	public function encrypt_string( $string ) {

		$encryption_key = wp_salt();

		return base64_encode( openssl_encrypt(
			$string,
			'aes-256-cbc',
			$encryption_key,
			0,
			substr( $encryption_key, 0, 16 )
		) );
	}

	/**
	 * Decrypt a string using a secure method.
	 *
	 * @param string $string
	 * @return string|null
	 */
	public function decrypt_string( $string ) {

		$encryption_key = wp_salt();

		return openssl_decrypt(
			base64_decode( $string ),
			'aes-256-cbc',
			$encryption_key,
			0,
			substr( $encryption_key, 0, 16 )
		);
	}

	/**
	 * Get AI API key from the WordPress options table.
	 *
	 * @return string|null
	 */
	public function get_key() {
		$encrypted_key = get_option( self::API_KEY_STORAGE );
		return $this->decrypt_string( $encrypted_key );
	}

	/**
	 * Get the list of models from the WordPress options table.
	 *
	 * @return array
	 */
	public function get_models() {
		$models = get_transient( self::MODELS_STORAGE );
		return is_array( $models ) ? $models : [];
	}

	/**
	 * Set the list of models in the WordPress options table.
	 *
	 * @param array $models
	 * @return void
	 */
	public function set_models( array $models ) {
		if ( empty( $models ) ) {
			delete_transient( self::MODELS_STORAGE );
		} else {
			set_transient( self::MODELS_STORAGE, $models, 2 * HOUR_IN_SECONDS );
		}
	}

	public function set_model( $model ) {
		if ( ! $model ) {
			delete_option( self::SELECTED_MODEL );
		} else {
			update_option( self::SELECTED_MODEL, $model );
		}
	}

	public function get_model() {
		return get_option( self::SELECTED_MODEL, 'gpt-5-mini' );
	}
}
