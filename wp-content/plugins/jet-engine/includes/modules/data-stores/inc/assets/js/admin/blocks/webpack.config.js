var path = require('path');
var webpack = require('webpack');

const WPExtractorPlugin   = require(
	'@wordpress/dependency-extraction-webpack-plugin',
);

module.exports = {
	name: 'blocks',
	context: path.resolve( __dirname, 'src' ),
	entry: {
		'blocks.js': 'blocks.js',
		// 'jet-forms.js': './jet-forms.action.js',
		'jet-forms-v2.js': './jet-forms-v2/index.js',
	},
	output: {
		path: path.resolve( __dirname, 'build' ),
		filename: '[name]'
	},
	resolve: {
		modules: [
			path.resolve( __dirname, 'src' ),
			'node_modules'
		],
		extensions: [ '.js', '.jsx' ],
		alias: {
			'@': path.resolve( __dirname, 'src' ),
			'blocks': path.resolve( __dirname, 'src/js/blocks/' ),
		}
	},
	plugins: [
		new WPExtractorPlugin(),
	],
	optimization: {
		splitChunks: {
			chunks: 'all',
		},
	},
	module: {
		rules: [
			{
				test: /\.jsx?$/,
				loader: 'babel-loader',
				exclude: /node_modules/
			}
		]
	},
	externalsType: 'window',
	externals: {
		'jet-form-builder-components': [ 'jfb', 'components' ],
		'jet-form-builder-data': [ 'jfb', 'data' ],
		'jet-form-builder-actions': [ 'jfb', 'actions' ],
		'jet-form-builder-blocks-to-actions': [ 'jfb', 'blocksToActions' ],
	},
}
