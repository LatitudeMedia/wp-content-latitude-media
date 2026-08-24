/**
 * Playwright configuration for the ltm-core E2E suite.
 *
 * Extends the config bundled with @wordpress/scripts, which supplies the
 * global setup that logs in as admin once and reuses the storage state, plus
 * sensible artifact/trace/retry defaults.
 *
 * Two things must be overridden:
 *   - testDir: the bundled config points at ./specs relative to itself.
 *   - webServer.command: the bundled config runs `npm run wp-env start`, but
 *     this package names that script `env:start`.
 *
 * Targets the wp-env *tests* instance on port 8889, leaving the development
 * instance (8888) free for manual work.
 */

/**
 * External dependencies
 */
const path = require( 'path' );
const { defineConfig } = require( '@playwright/test' );

/**
 * WordPress dependencies
 */
const baseConfig = require( '@wordpress/scripts/config/playwright.config' );

process.env.WP_BASE_URL ??= 'http://localhost:8889';
process.env.WP_ARTIFACTS_PATH ??= path.join( __dirname, 'artifacts' );

module.exports = defineConfig( {
	...baseConfig,
	testDir: path.join( __dirname, 'specs' ),
	// Gates the suite on ltm-core being provisioned before delegating to the
	// wp-scripts setup. webServer only waits for the port to open, which on a
	// cold wp-env start happens before the plugin is active.
	globalSetup: require.resolve( './specs/global-setup.js' ),
	// specs/global-setup.js lives in testDir but is not a spec.
	testIgnore: [ '**/global-setup.js' ],
	// The bundled default is 5s. Assertions that wait on an SSR preview or a
	// live REST search (e.g. the Featured Post block's combobox) need more
	// room on this stack than a stock Gutenberg install — same rationale as
	// the raised actionTimeout below.
	expect: {
		...baseConfig.expect,
		timeout: 15_000,
	},
	use: {
		...baseConfig.use,
		baseURL: process.env.WP_BASE_URL,
		// The bundled default is 10s, tuned for a stock Gutenberg install. This
		// site also loads Yoast, ACF Pro and a large theme, so editor actions
		// have meaningfully more work to do.
		actionTimeout: 30_000,
	},
	webServer: {
		...baseConfig.webServer,
		command: 'npm run env:start',
		port: 8889,
		reuseExistingServer: true,
	},
} );
