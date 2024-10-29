const path = require("path");
var webpack = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const RemoveEmptyScriptsPlugin = require('webpack-remove-empty-scripts');
var mode = "development";
var sourceMap = "source-map";

module.exports = (env) => {
  if (env.production === true) {
    mode = "production";
    sourceMap = "hidden-source-map";
  }

  return {
    mode: mode,
    devtool: sourceMap,
    optimization: {
      minimize: true,
    },
    entry: {
      'css/base': './src/assets/scss/base/base_loader.scss',
      'css/fonts': './src/assets/scss/fonts/fonts_loader.scss',
      'css/header': './src/assets/scss/header/header_loader.scss',
      'css/homepage': './src/assets/scss/homepage/homepage_loader.scss',
      'css/pages': './src/assets/scss/pages/pages_loader.scss',
      'css/footer': './src/assets/scss/footer/footer_loader.scss',
      'js/custom': './src/assets/js/custom.js'
    },
    output: {
      path: path.resolve(__dirname, "dist"),
      filename: "[name].min.js",
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /(node_modules|bower_components)/,
          use: {
            loader: "babel-loader",
          },
        },
        {
          test: /\.(css|sass|scss)$/,
          exclude: /(node_modules|bower_components)/,
          use: [
            {
              loader: MiniCssExtractPlugin.loader,
            },
            {
              loader: "css-loader",
            },
            {
              loader: 'postcss-loader',   // Apply PostCSS transformations
              options: {
                postcssOptions: {
                  plugins: [
                    require('postcss-combine-media-query')() // Combine media queries
                  ]
                }
              }
            },
            {
              loader: "sass-loader",
            },

          ],
        },
        {
          test: /\.(png|jpg|ico|svg)$/i,
          exclude: /node_modules/,
          type: 'asset/resource',
          generator: {
            filename: 'images/[name][ext]'
          }
        },
        {
          test: /.(ttf|otf|eot|woff(2)?)(\?[a-z0-9]+)?$/,
          type: 'asset/resource',
          generator: {
            filename: 'fonts/[name][ext]',
          },
        },
      ],
    },
    plugins: [
      new RemoveEmptyScriptsPlugin(),
      new MiniCssExtractPlugin({
        filename: "[name].min.css",
      }),
    ],
  };
};
