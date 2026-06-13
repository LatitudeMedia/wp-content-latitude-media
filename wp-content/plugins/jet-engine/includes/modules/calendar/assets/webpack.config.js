const path              = require( 'path' );
const CopyWebpackPlugin = require("copy-webpack-plugin");

module.exports = {
	name: 'js_bundle',
	context: path.resolve( __dirname, 'src' ),
	entry: {
		'jet-form-builder-editor': './jet-form-builder-editor.js',
		'jet-form-builder-public': './jet-form-builder-public.js',
		'advanced-date-field': './advanced-date-field.js',
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
	plugins: [
		new CopyWebpackPlugin({
		  patterns: [
			{
			  from: path.resolve(__dirname, "css/advanced-date-field.css"), // Source file
			  to: path.resolve(__dirname, "../advanced-date-field/form-builder/advanced-date-field/", "style.css"), // Destination file
			},
		  ],
		}),
	  ],
	module: {
		rules: [
			{
				test: /\.jsx?$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
			{
				test: /\.scss$/,
				use: [
					'style-loader',
					'css-loader',
					'sass-loader',
				],
			},
			{
				test: /\.css$/,
				use: [
					'css-loader',
				],
			},
		],
	},
};
