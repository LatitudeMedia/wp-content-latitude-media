const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	resolve: {
		...defaultConfig.resolve,
		fullySpecified: false,
	},
	entry: {
		'agent-ui': path.resolve( __dirname, 'assets/src/agent-ui.js' ),
	},
	output: {
		path: path.resolve(__dirname, 'assets/build'),
		filename: '[name].js',
	},
};
