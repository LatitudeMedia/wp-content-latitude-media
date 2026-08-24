/**
 * Global setup for the ltm-core E2E suite.
 *
 * Playwright's `webServer` option only waits for the port to accept TCP
 * connections. On a cold `wp-env` start the port opens well before WordPress
 * has finished installing and activating the mapped plugins, so the first spec
 * can hit a half-provisioned site and get "Invalid post type." — while later
 * specs pass because provisioning finished in the meantime.
 *
 * This gates the suite on ltm-core actually being ready (its CPT registered
 * and exposed over REST) before handing off to the standard wp-scripts global
 * setup, which authenticates and saves the admin storage state.
 */

/**
 * External dependencies
 */
const { request } = require( '@playwright/test' );

/**
 * Internal dependencies
 */
const baseGlobalSetup = require( '@wordpress/scripts/config/playwright/global-setup' );

const READY_TIMEOUT_MS = 120_000;
const POLL_INTERVAL_MS = 2_000;

/**
 * Polls until the thematic-pages post type is registered and served over REST.
 *
 * @param {string} baseURL
 */
async function waitForPluginReady( baseURL ) {
	const requestContext = await request.newContext( { baseURL } );
	const deadline = Date.now() + READY_TIMEOUT_MS;
	let lastStatus = 'no response';

	try {
		while ( Date.now() < deadline ) {
			try {
				const response = await requestContext.get(
					'/wp-json/wp/v2/types/thematic-pages'
				);

				if ( response.ok() ) {
					return;
				}

				lastStatus = `HTTP ${ response.status() }`;
			} catch ( error ) {
				lastStatus = error.message;
			}

			await new Promise( ( resolve ) =>
				setTimeout( resolve, POLL_INTERVAL_MS )
			);
		}

		throw new Error(
			`ltm-core was not ready within ${
				READY_TIMEOUT_MS / 1000
			}s (last result: ${ lastStatus }). ` +
				'Check that wp-env started and that the plugin is active: ' +
				'`npx wp-env run tests-cli wp plugin list`.'
		);
	} finally {
		await requestContext.dispose();
	}
}

/**
 * @param {import('@playwright/test').FullConfig} config
 */
module.exports = async function globalSetup( config ) {
	const { baseURL } = config.projects[ 0 ].use;

	await waitForPluginReady( baseURL );

	return baseGlobalSetup( config );
};
