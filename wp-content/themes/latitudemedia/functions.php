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
 * Modify menu structure
 */
require_once ("inc/menu/menu.php");

/**
 * Helper functions
 */
require_once ("inc/helpers.php");

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