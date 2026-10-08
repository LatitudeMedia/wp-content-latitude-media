<?php
namespace LTMCore\Blocks;

/**
 * Cache-busts the stylesheets of every block in this plugin's build/.
 *
 * @package LTMCore
 */
class AssetVersioning {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'block_type_metadata', array( $this, 'version_from_build_files' ) );
	}

	/**
	 * Replace block.json version with the latest file mtime to break cache.
	 */
	public function version_from_build_files( $metadata ) {
		if ( empty( $metadata['file'] ) || ! str_starts_with( $metadata['file'], wp_normalize_path( LTM_CORE_DIR . '/build/' ) ) ) {
			return $metadata;
		}

		$mtimes = array_map( 'filemtime', glob( dirname( $metadata['file'] ) . '/*.css' ) ?: array() );

		if ( $mtimes ) {
			$metadata['version'] = (string) max( $mtimes );
		}

		return $metadata;
	}
}
