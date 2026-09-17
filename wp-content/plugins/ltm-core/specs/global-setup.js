/**
 * Global setup for the ltm-core E2E suite.
 *
 * Gates the suite on the tests site actually being provisioned, and provisions
 * it if it is not, before handing off to the standard wp-scripts global setup
 * (which authenticates and saves the admin storage state).
 *
 * Two distinct ways the site arrives unprovisioned:
 *
 *   1. Cold start. Playwright's `webServer` option only waits for the port to
 *      accept TCP connections. On a cold `wp-env start` the port opens well
 *      before WordPress has finished installing and activating mapped plugins.
 *
 *   2. `npm run test:php`. PHPUnit reinstalls WordPress into the same database
 *      that serves the tests site on 8889, which empties `active_plugins` and
 *      `permalink_structure`. Running PHPUnit therefore un-provisions the E2E
 *      site, and `wp-env start` will not fix it because the containers are
 *      already up, so its afterStart lifecycle script never re-runs. Before
 *      this, running the suites in that order failed every spec with
 *      `rest_no_route`.
 *
 * So waiting alone is not enough — case 2 never resolves on its own. When the
 * site is not ready we run bin/wp-env-after-start.sh (the same idempotent
 * provisioning wp-env runs on start) and re-check.
 *
 * The readiness probe deliberately parses the response body rather than
 * trusting `response.ok()`. With `permalink_structure` wiped, /wp-json/ does
 * not route and WordPress answers with an empty 200 text/html page — which
 * satisfies `.ok()`, so the old check passed against a completely
 * unprovisioned site and left the failure to surface as confusing
 * `rest_no_route` errors inside individual specs. Requiring the expected JSON
 * payload proves the plugin is active *and* pretty permalinks are flushed,
 * which is what the specs actually depend on.
 */

/**
 * External dependencies
 */
const { execFileSync } = require( 'child_process' );
const path = require( 'path' );
const { request } = require( '@playwright/test' );

/**
 * Internal dependencies
 */
const baseGlobalSetup = require( '@wordpress/scripts/config/playwright/global-setup' );

const READY_TIMEOUT_MS = 120_000;
const POLL_INTERVAL_MS = 2_000;
const PROVISION_SCRIPT = path.join(
	__dirname,
	'..',
	'bin',
	'wp-env-after-start.sh'
);

/**
 * Checks whether the tests site is provisioned.
 *
 * Resolves to null when ready, or a short human-readable reason when not.
 *
 * @param {import('@playwright/test').APIRequestContext} requestContext
 * @return {Promise<string|null>} Reason it is not ready, or null if it is.
 */
async function readinessFailure( requestContext ) {
	let response;

	try {
		response = await requestContext.get(
			'/wp-json/wp/v2/types/thematic-pages'
		);
	} catch ( error ) {
		return error.message;
	}

	if ( ! response.ok() ) {
		return `HTTP ${ response.status() }`;
	}

	let body;
	try {
		body = await response.json();
	} catch {
		// The empty-200-HTML case: WordPress served a page, not the REST API,
		// because pretty permalinks are not in place.
		return 'response was not JSON (pretty permalinks are probably not flushed)';
	}

	if ( body?.slug !== 'thematic-pages' ) {
		return `unexpected REST payload (${ JSON.stringify( body ).slice(
			0,
			120
		) })`;
	}

	return null;
}

/**
 * Polls until the tests site is provisioned or the deadline passes.
 *
 * @param {import('@playwright/test').APIRequestContext} requestContext
 * @param {number}                                       timeoutMs
 * @return {Promise<string|null>} Last failure reason, or null once ready.
 */
async function waitUntilReady( requestContext, timeoutMs ) {
	const deadline = Date.now() + timeoutMs;
	let failure = await readinessFailure( requestContext );

	while ( failure !== null && Date.now() < deadline ) {
		await new Promise( ( resolve ) =>
			setTimeout( resolve, POLL_INTERVAL_MS )
		);
		failure = await readinessFailure( requestContext );
	}

	return failure;
}

/**
 * Runs the idempotent wp-env provisioning script.
 *
 * The script invokes `wp-env` unqualified, which is only on PATH under npm's
 * script environment — this may be called from a bare `playwright test`, so
 * node_modules/.bin is added explicitly.
 */
function provision() {
	const binDir = path.join( __dirname, '..', 'node_modules', '.bin' );

	execFileSync( 'bash', [ PROVISION_SCRIPT ], {
		stdio: 'inherit',
		env: {
			...process.env,
			PATH: `${ binDir }${ path.delimiter }${ process.env.PATH }`,
		},
	} );
}

/**
 * @param {import('@playwright/test').FullConfig} config
 */
module.exports = async function globalSetup( config ) {
	const { baseURL } = config.projects[ 0 ].use;
	const requestContext = await request.newContext( { baseURL } );

	try {
		// A cold `wp-env start` needs time; an un-provisioned site needs the
		// script. Wait briefly first so the common cold-start case does not pay
		// for a redundant provisioning run.
		let failure = await waitUntilReady( requestContext, 20_000 );

		if ( failure !== null ) {
			// eslint-disable-next-line no-console
			console.log(
				`[ltm-core] Tests site is not ready (${ failure }). Provisioning...`
			);
			provision();

			failure = await waitUntilReady( requestContext, READY_TIMEOUT_MS );
		}

		if ( failure !== null ) {
			throw new Error(
				`ltm-core was not ready after provisioning (last result: ${ failure }). ` +
					'Check that wp-env started and that the plugins are active: ' +
					'`npx wp-env run tests-cli wp plugin list`.'
			);
		}
	} finally {
		await requestContext.dispose();
	}

	return baseGlobalSetup( config );
};
