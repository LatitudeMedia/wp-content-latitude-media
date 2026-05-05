var path = require('path');
var webpack = require('webpack');

module.exports = {
	name: 'blocks',
	context: path.resolve( __dirname, 'src' ),
	entry: {
		'js/admin/blocks.js': 'index.js',
		'js/frontend-maps.js': 'frontend-maps.js',
		'js/public/google-maps.js': 'public/google-maps.js',
		'js/public/leaflet-maps.js': 'public/leaflet-maps.js',
		'js/public/location-distance.js': 'public/location-distance.js',
		'js/public/map-sync.js': 'public/map-sync.js',
		'js/public/mapbox-maps.js': 'public/mapbox-maps.js',
		'js/public/mapbox-markerclusterer.js': 'public/mapbox-markerclusterer.js',
		'js/public/user-geolocation.js': 'public/user-geolocation.js',
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
