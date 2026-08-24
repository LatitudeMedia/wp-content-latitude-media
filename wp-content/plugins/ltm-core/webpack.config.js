/**
 * Extends the default wp-scripts webpack config with one extra entry point.
 *
 * wp-scripts' default config discovers entries by scanning src/**\/block.json
 * (needed for the --blocks-manifest block registration used by the other
 * blocks in this plugin). The "Right Sidebar Layout" core/group variation
 * has no block.json — it's not a new block type, just registerBlockVariation
 * + nothing else — so it isn't picked up by that scan and needs an explicit
 * entry here.
 */

/**
 * External dependencies
 */
const path = require( 'path' );

/**
 * WordPress dependencies
 */
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// wp-scripts exports a single script-config object, unless
// --experimental-modules is passed (not used by this plugin's package.json
// scripts), in which case it exports [ scriptConfig, moduleConfig ].
const scriptConfig = Array.isArray( defaultConfig )
	? defaultConfig[ 0 ]
	: defaultConfig;

// `entry` is a function in the installed @wordpress/scripts version — it
// re-scans src/**/block.json on every compile, which is what makes
// --blocks-manifest block.json discovery work. Guard for a plain-object
// shape too, in case a future @wordpress/scripts version changes this.
function getBlockEntries() {
	return typeof scriptConfig.entry === 'function'
		? scriptConfig.entry()
		: scriptConfig.entry;
}

module.exports = {
	...scriptConfig,
	entry() {
		return {
			...getBlockEntries(),
			'right-sidebar-layout/index': path.resolve(
				process.cwd(),
				'src/right-sidebar-layout/index.js'
			),
		};
	},
};
