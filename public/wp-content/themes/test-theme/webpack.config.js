const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const glob = require('glob');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

// ブロック自動取得
const entries = {};
const srcDir = './block-editor/src';
glob.sync('**/index.js', {
  ignore: ['./block-editor/src/js', './block-editor/src/scss'],
  cwd: srcDir,
}).map((key) => {
  entries[key] = path.resolve(srcDir, key);
});

// ↓ CleanWebpackPlugin が 先頭じゃなくなったとき用
for (let i = 0; i < defaultConfig.plugins.length; i++) {
  const pluginInstance = defaultConfig.plugins[i];
  if ('CleanWebpackPlugin' === pluginInstance.constructor.name) {
    defaultConfig.plugins.splice(i, i);
  }
}

module.exports = {
  ...defaultConfig, //@wordpress/scriptを引き継ぐ

  mode: 'production',
  entry: entries,
  output: {
    path: path.resolve(__dirname, "block-editor", "dist"),
    filename: '[name]',
  },

  resolve: {
    alias: {
      '@blocks': path.resolve(__dirname, "block-editor", "src"),
    },
  },

  // block.jsonファイルをコピー。
  plugins: [
    ...defaultConfig.plugins,

    new CleanWebpackPlugin({
      cleanOnceBeforeBuildPatterns: [
        'blocks/**',
      ],
      // 以下の設定は@wordpress/scripts/config/webpack.configからコピー。
      cleanAfterEveryBuildPatterns: [
        '!fonts/**',
        '!images/**'
      ],
      cleanStaleWebpackAssets: false,
    }),

    new CopyWebpackPlugin({
      patterns: [
        {
          context: path.resolve(__dirname, "block-editor", "src"),
          from: "**/block.json",
          to: path.resolve(__dirname, "block-editor", "dist"),
        },
      ]
    }),
  ],
};
