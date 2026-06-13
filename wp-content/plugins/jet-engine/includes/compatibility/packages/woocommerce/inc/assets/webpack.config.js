const path              = require( 'path' );
const CopyWebpackPlugin = require("copy-webpack-plugin");

module.exports = {
	name: 'js_bundle',
	context: path.resolve( __dirname, 'src' ),
	entry: {
		'blocks-views': './blocks-views.js',
	},
	output: {
		path: path.resolve( __dirname, 'js' ),
		filename: '[name].js',
	},
	devtool: 'inline-cheap-module-source-map',
	resolve: {
		modules: [
			path.resolve( __dirname, 'src' ),
			'node_modules',
		],
		extensions: [ '.js', '.jsx' ],
		alias: {
			'@': path.resolve( __dirname, 'src' ),
		},
	},
	plugins: [],
	module: {
		rules: [
			{
				test: /\.jsx?$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			}
		],
	},
};
