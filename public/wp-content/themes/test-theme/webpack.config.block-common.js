const path = require('path');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const FixStyleOnlyEntriesPlugin = require("webpack-fix-style-only-entries");

module.exports = {
  mode: 'production',
  entry: {
    'js/bundle': './block-editor/src/js/bundle.js',
    'css/index': './block-editor/src/scss/index.scss',
    'css/style-index': './block-editor/src/scss/style-index.scss'
  },
  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, 'block-editor', 'dist'),
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
      {
        test: /\.scss$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
      },
    ],
  },
  plugins: [
    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [
        '!blocks/**',
      ],
    }),
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
    new FixStyleOnlyEntriesPlugin(),
  ],
  // node_modules を監視（watch）対象から除外
  watchOptions: {
    ignored: /node_modules/
  },
};
