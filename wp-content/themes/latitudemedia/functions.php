<?php
/**
 * Latitude Media functions and definitions
 *
 */


/**
 * Instance trait
 */
require_once ("inc/trait-instance.php");

/**
 * Setup theme
 */
require_once ("inc/media.php");
require_once ("inc/theme.php");
require_once ("inc/override.php");
require_once ("inc/latitudemedia-autoloader.php");

/**
 * Auto-activate the ltm-core plugin if it's installed but not active —
 * the sponsor taxonomy/CPT (and get_post_sponsor(), used by is_sponsored())
 * now live there, so sponsor features won't work correctly without it.
 */
function ltm_core_plugin_maybe_activate() {
    if ( class_exists( '\LTMCore\Taxonomies\PostSponsor' ) ) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    $plugin = 'ltm-core/ltm-core.php';
    if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
        activate_plugin( $plugin );
    }
}
add_action( 'admin_init', 'ltm_core_plugin_maybe_activate' );


/**
 * Modify menu structure
 */
require_once ("inc/menu/menu.php");

/**
 * Helper functions
 */
require_once ("inc/helpers.php");

/**
 * Ads functions
 */
require_once ("inc/ads.php");

/**
 * Include ACF configs
 */
require_once('inc/acf/init.php');

/**
 * Enqueue styles and scripts
 */
require_once ("inc/enqueue.php");

/**
 * Manage user queries
 */
require_once ("inc/class-data-manage.php");

require_once ("inc/class-page-data.php");

require_once ("inc/template-tags.php");



require_once('inc/cli.php');


/**
 * Customize admin area
 *
 */
require_once('inc/admin-customize.php');

/**
 * Add custom schema data
 *
 */ 
add_filter( 'wpseo_schema_organization', 'latitude_media_extend_organization_schema', 11, 2 );

function latitude_media_extend_organization_schema( $data, $context ) {
    // Add a simple location object
    $data['location'] = [
        '@type' => 'Place',
        'name' => 'Remote / Distributed',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => '444 Somerville Ave',
            'addressLocality' => 'Somerville',
            'addressRegion' => 'MA',
            'postalCode' => '02143',
            'addressCountry' => 'US'
        ]
    ];

    // Add area served (United States)
    $data['areaServed'] = [
        '@type' => 'Country',
        'name' => 'United States'
    ];

    return $data;
}
