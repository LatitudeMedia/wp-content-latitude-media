var path = require('path');
var webpack = require('webpack');

module.exports = {
	name: 'blocks',
	context: path.resolve( __dirname, 'src' ),
	entry: {
		'frontend.js': 'main.js',
		'modules/calendar.js': 'modules/calendar.js',
		'modules/data-stores.js': 'modules/data-stores.js',
		'modules/jet-popup.js': 'modules/jet-popup.js',
		'modules/jet-smart-filters.js': 'modules/jet-smart-filters.js',
	},
	output: {
		path: path.resolve( __dirname ),
		filename: '[name]'
	},
	resolve: {
		modules: [
			path.resolve( __dirname, 'src' ),
			'node_modules'
		],
		extensions: [ '.js' ],
		alias: {
			'@': path.resolve( __dirname, 'src' ),
		}
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/
			}
		]
	}
}
