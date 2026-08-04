<?php
namespace LTMCore\Blocks;

/**
 * Registers this plugin's block types and keeps the Title Block restricted
 * to Thematic Pages.
 *
 * @package LTMCore
 */
class Title {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_blocks' ) );
		add_filter( 'allowed_block_types_all', array( $this, 'restrict_to_thematic_pages' ), 10, 2 );
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata. Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	public function register_blocks() {
		wp_register_block_types_from_metadata_collection( LTM_CORE_DIR . '/build', LTM_CORE_DIR . '/build/blocks-manifest.php' );
	}

	/**
	 * Keeps the Title Block out of the inserter everywhere except Thematic Pages.
	 */
	public function restrict_to_thematic_pages( $allowed_block_types, $editor_context ) {
		$block_name = 'latitudemedia/title-block';
		$post_type  = 'thematic-pages';

		if ( empty( $editor_context->post ) || $editor_context->post->post_type === $post_type ) {
			return $allowed_block_types;
		}

		if ( ! is_array( $allowed_block_types ) || empty( $allowed_block_types ) ) {
			$registered_blocks   = \WP_Block_Type_Registry::get_instance()->get_all_registered();
			$allowed_block_types = array_keys( $registered_blocks );
		}

		return array_values( array_diff( $allowed_block_types, [ $block_name ] ) );
	}
}
