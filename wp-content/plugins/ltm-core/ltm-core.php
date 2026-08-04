<?php
/**
 * Plugin Name:       Latitude Media Core
 * Description:       Native Gutenberg blocks for Latitude Media (Title Block, Featured Post) and custom post types, taxonomies, etc.
 * Version:           1.0.0
 * Requires at least: 7.0.2
 * Requires PHP:      8.3
 * Author:            Latitude Media
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ltm
 *
 * @package LTMCore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'LTM_CORE_DIR', __DIR__ );

/**
 * Flushes rewrite rules once on activation, so the thematic-pages CPT's
 * custom `/themes/...` permalinks work immediately instead of 404ing until
 * someone happens to re-save Settings > Permalinks. The post type and
 * taxonomy are (re-)registered explicitly here first — activation can run
 * via WP-CLI or the REST API, not just wp-admin, so we don't rely on the
 * `init`-time registration above having already run in the same request.
 */
function ltm_core_activate() {
	( new \LTMCore\Taxonomies\ThematicPageTypes() )->create_taxonomy();
	( new \LTMCore\PostTypes\ThematicPages() )->create_post_type();
	( new \LTMCore\Taxonomies\PostSponsor() )->create_taxonomy();
	( new \LTMCore\PostTypes\Sponsors() )->create_post_type();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ltm_core_activate' );

function ltm_core_loader() {

	require_once __DIR__ . '/includes/functions.php';
	require_once __DIR__ . '/includes/Taxonomies/ThematicPageTypes.php';
	require_once __DIR__ . '/includes/PostTypes/ThematicPages.php';
	require_once __DIR__ . '/includes/Taxonomies/PostSponsor.php';
	require_once __DIR__ . '/includes/PostTypes/Sponsors.php';
	require_once __DIR__ . '/includes/RestApi/FeaturedPostSearch.php';
	require_once __DIR__ . '/includes/Blocks/Title.php';
	require_once __DIR__ . '/includes/Blocks/CategoryPostListing.php';

	// Instantiated at file-load time (not inside a hook) so each class's own
	// `add_action( 'init', ... )` self-registration registers cleanly before
	// WordPress's `init` action starts firing — adding a new `init` callback
	// from inside an already-executing `init` callback is unreliable.
	new \LTMCore\PostTypes\ThematicPages();
	new \LTMCore\Taxonomies\ThematicPageTypes();
	new \LTMCore\Taxonomies\PostSponsor();
	new \LTMCore\PostTypes\Sponsors();
	new \LTMCore\RestApi\FeaturedPostSearch();
	new \LTMCore\Blocks\Title();
};
ltm_core_loader();