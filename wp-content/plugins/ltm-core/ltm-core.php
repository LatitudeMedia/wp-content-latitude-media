<?php
/**
 * Plugin Name:       Latitude Media Core
 * Description:       Native Gutenberg blocks for Latitude Media (Title Block, Featured Single Item) and custom post types, taxonomies, etc.
 * Version:           1.1.1
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
	( new \LTMCore\PostTypes\Events() )->create_post_type();
	( new \LTMCore\PostTypes\Speakers() )->create_post_type();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'ltm_core_activate' );

function ltm_core_loader() {

	require_once __DIR__ . '/includes/functions.php';

	// PSR-4 autoloader: \LTMCore\PostTypes\Events maps to includes/PostTypes/Events.php.
	spl_autoload_register(
		function ( $class ) {
			$prefix = 'LTMCore\\';
			if ( ! str_starts_with( $class, $prefix ) ) {
				return;
			}
			$file = __DIR__ . '/includes/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
			if ( file_exists( $file ) ) {
				require $file;
			}
		}
	);

	// Instantiated at file-load time (not inside a hook) so each class's own
	// `add_action( 'init', ... )` self-registration registers cleanly before
	// WordPress's `init` action starts firing — adding a new `init` callback
	// from inside an already-executing `init` callback is unreliable.
	new \LTMCore\PostTypes\ThematicPages();
	new \LTMCore\Taxonomies\ThematicPageTypes();
	new \LTMCore\Taxonomies\PostSponsor();
	new \LTMCore\PostTypes\Sponsors();
	new \LTMCore\PostTypes\Events();
	new \LTMCore\PostTypes\Speakers();
	new \LTMCore\RestApi\FeaturedSingleItemSearch();
	new \LTMCore\Blocks\Shared\AssetVersioning();
	new \LTMCore\Blocks\Title();
	new \LTMCore\Blocks\RightSidebarLayout();
	new \LTMCore\Blocks\EventAgenda();
	new \LTMCore\Blocks\EventAgendaV2();
	new \LTMCore\Blocks\EventPreview();
	new \LTMCore\Blocks\EventShortDescription();
	new \LTMCore\Blocks\EventDescription();
	new \LTMCore\Blocks\EventGrayIcon();
	new \LTMCore\Blocks\EventNavigationMenu();
	new \LTMCore\Blocks\EventSpeakers();
	new \LTMCore\Blocks\EventSponsors();
	new \LTMCore\Blocks\EventContactUs();
	new \LTMCore\Blocks\ImageAndText();
	new \LTMCore\Blocks\StyledButton();
	new \LTMCore\Blocks\EventsList();
	new \LTMCore\Blocks\EventVenue();
	new \LTMCore\Blocks\RecapVideo();
};
ltm_core_loader();