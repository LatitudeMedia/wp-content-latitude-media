<?php
namespace LTMCore\Blocks;

/**
 * Registers the "Right Sidebar Layout" core/columns variation's editor
 * bundle.
 *
 * This is a variation of core/columns, not a new block type — it has no
 * block.json and isn't wired through
 * wp_register_block_types_from_metadata_collection() like Title.php; its
 * compiled JS is enqueued directly from build/right-sidebar-layout/.
 *
 * @package LTMCore
 */
class RightSidebarLayout {

	const HANDLE = 'ltm-right-sidebar-layout';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Loads the variation-registration script.
	 */
	public function enqueue_editor_assets() {
		$asset_file = LTM_CORE_DIR . '/build/right-sidebar-layout/index.asset.php';

		if ( ! file_exists( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			self::HANDLE,
			plugins_url( 'build/right-sidebar-layout/index.js', LTM_CORE_DIR . '/ltm-core.php' ),
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_set_script_translations( self::HANDLE, 'ltm' );
	}
}
