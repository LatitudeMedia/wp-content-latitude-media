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
      //Admin assets
      'css/admin': './src/assets/scss/admin/admin.scss',
      'css/editor': './src/assets/scss/admin/editor.scss',
      'css/base': './src/assets/scss/base/base_loader.scss',
      'css/fonts': './src/assets/scss/fonts/fonts_loader.scss',
      'css/header': './src/assets/scss/header/header_loader.scss',
      'css/homepage': './src/assets/scss/homepage/homepage_loader.scss',
      'css/pages': './src/assets/scss/pages/pages_loader.scss',
      'css/footer': './src/assets/scss/footer/footer_loader.scss',
      'js/custom': './src/assets/js/custom.js',
      'js/load-more': './src/assets/js/load-more.js',
      'js/dfp-ads': './src/assets/js/dfp-ads.js',
      //Blocks styles
      'css/blocks/news-plates-section'      : './src/assets/scss/blocks/news-plates-section.scss',
      'css/blocks/featured-research-block'  : './src/assets/scss/blocks/featured-research-block.scss',
      'css/blocks/research-banner-block'    : './src/assets/scss/blocks/research-banner-block.scss',
      'css/blocks/research-overview-block'  : './src/assets/scss/blocks/research-overview-block.scss',
      'css/blocks/image-and-text'            : './src/assets/scss/blocks/image-and-text.scss',
      'css/blocks/content-with-background-block': './src/assets/scss/blocks/content-with-background-block.scss',
      'css/blocks/authors-list-block'       : './src/assets/scss/blocks/authors-list-block.scss',
      'css/blocks/order-form-block'         : './src/assets/scss/blocks/order-form-block.scss',
      'css/blocks/subscribe-form-block'     : './src/assets/scss/blocks/subscribe-form-block.scss',
      'css/blocks/categories-section-block' : './src/assets/scss/blocks/categories-section-block.scss',
      'css/blocks/news-with-hero'           : './src/assets/scss/blocks/news-with-hero.scss',
      'css/blocks/news-with-sidebar-section-block': './src/assets/scss/blocks/news-with-sidebar-section-block.scss',
      'css/blocks/sidebar-info-block'       : './src/assets/scss/blocks/sidebar-info-block.scss',
      'css/blocks/large-podcasts-section'   : './src/assets/scss/blocks/large-podcasts-section.scss',
      'css/blocks/partner-porcasts-block'   : './src/assets/scss/blocks/partner-porcasts-block.scss',
      'css/blocks/info-cta-block'           : './src/assets/scss/blocks/info-cta-block.scss',
      'css/blocks/news-list-section'        : './src/assets/scss/blocks/news-list-section.scss',
      'css/blocks/spotlight-quote-section'  : './src/assets/scss/blocks/spotlight-quote-section.scss',
      'css/blocks/signup-form-section'      : './src/assets/scss/blocks/signup-form-section.scss',
      'css/blocks/sidebar-editors-picks-section': './src/assets/scss/blocks/sidebar-editors-picks-section.scss',
      'css/blocks/related-reading-section'  : './src/assets/scss/blocks/related-reading-section.scss',
      'css/blocks/events-list-block'        : './src/assets/scss/blocks/events-list-block.scss',
      'js/blocks/load-more-events'          : './src/assets/js/blocks/load-more-events.js',
      'css/blocks/event-preview-block'      : './src/assets/scss/blocks/event-preview-block.scss',
      'css/blocks/event-description-block'  : './src/assets/scss/blocks/event-description-block.scss',
      'css/blocks/event-sponsors-block'     : './src/assets/scss/blocks/event-sponsors-block.scss',
      'css/blocks/event-agenda-block'       : './src/assets/scss/blocks/event-agenda-block.scss',
      'css/blocks/event-venue-block'        : './src/assets/scss/blocks/event-venue-block.scss',
      'css/blocks/event-gray-icon-block'    : './src/assets/scss/blocks/event-gray-icon-block.scss',
      'css/blocks/event-partners-block'     : './src/assets/scss/blocks/event-partners-block.scss',
      'css/blocks/event-about-sponsors-block': './src/assets/scss/blocks/event-about-sponsors-block.scss',
      'css/blocks/page-hero-block'          : './src/assets/scss/blocks/page-hero-block.scss',
      'css/blocks/downloads-info-block'     : './src/assets/scss/blocks/downloads-info-block.scss',
      'css/blocks/listeners-info-block'     : './src/assets/scss/blocks/listeners-info-block.scss',
      'css/blocks/advertising-options-block': './src/assets/scss/blocks/advertising-options-block.scss',
      'css/blocks/our-approach-block'       : './src/assets/scss/blocks/our-approach-block.scss',
      'css/blocks/sample-campaign-block'    : './src/assets/scss/blocks/sample-campaign-block.scss',
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
