<?php
/**
 * PHPUnit bootstrap for the ltm-core plugin.
 *
 * Loads the WordPress core test library, then hooks this plugin in early
 * enough that its `init`-time post type / taxonomy registration runs inside
 * the test WordPress install.
 *
 * @package LTMCore
 */

// Composer autoloader — provides PHPUnit and the Yoast PHPUnit Polyfills.
$ltm_autoload = dirname( __DIR__ ) . '/vendor/autoload.php';
if ( ! file_exists( $ltm_autoload ) ) {
	echo "Could not find vendor/autoload.php. Run `composer install` in the plugin directory first." . PHP_EOL;
	exit( 1 );
}
require_once $ltm_autoload;

// The WP core test library. Inside wp-env's containers this is set to
// /wordpress-phpunit; fall back to the conventional local path.
$ltm_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $ltm_tests_dir ) {
	$ltm_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}
$ltm_tests_dir = rtrim( $ltm_tests_dir, '/\\' );

if ( ! file_exists( $ltm_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find the WordPress test library at {$ltm_tests_dir}." . PHP_EOL;
	echo "Run the suite through wp-env (`npm run test:php`), or set WP_TESTS_DIR." . PHP_EOL;
	exit( 1 );
}

// Point the polyfills at the installed Yoast package (required by WP core's
// test suite when it is not bundled).
if ( ! defined( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' ) ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', dirname( __DIR__ ) . '/vendor/yoast/phpunit-polyfills' );
}

// Give access to tests_add_filter().
require_once $ltm_tests_dir . '/includes/functions.php';

/**
 * Loads this plugin before WordPress finishes booting.
 *
 * `muplugins_loaded` fires before `init`, so ltm-core.php's load-time class
 * instantiation registers its `init` callbacks in time.
 */
function ltm_core_manually_load_plugin() {
	require dirname( __DIR__ ) . '/ltm-core.php';
}
tests_add_filter( 'muplugins_loaded', 'ltm_core_manually_load_plugin' );

// Start up the WP testing environment.
require $ltm_tests_dir . '/includes/bootstrap.php';
