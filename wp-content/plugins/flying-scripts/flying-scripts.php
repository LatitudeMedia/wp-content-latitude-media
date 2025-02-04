<?php
/**
 * Plugin Name: Flying Scripts: Delay JavaScript to Improve Site Speed & Performance
 * Plugin URI: https://wordpress.org/plugins/flying-scripts/
 * Description: Delay JavaScript to boost speed by loading scripts only when needed, reducing render-blocking for faster loading and a smoother user experience.
 * Author: WP Speed Matters
 * Author URI: https://wpspeedmatters.com/
 * Version: 1.2.4
 * Text Domain: flying-scripts
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

// Define constant with current version
define('FLYING_SCRIPTS_VERSION', '1.2.4');

include('init-config.php');
include('settings/index.php');
include('inject-js.php');
include('html-rewrite.php');
include('shortcuts.php');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'flying_scripts_add_shortcuts');