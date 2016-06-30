var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');

var config = {
  entry: [
    'webpack-dev-server/client?http://localhost:3000',
    'webpack/hot/only-dev-server',
    'react-hot-loader/patch',
    './index.js',
  ],
  output: {
    filename: 'bundle.js',
    publicPath: '/build',
    path: './build',
  },
  module: {
    loaders: [
      {
        test: /\.(scss|css)$/,
        exclude: /client\/build/,
        loader: ExtractTextPlugin.extract('css!sass'),
      },
      {
        test: /\.(js|jsx|babel)$/,
        exclude: /node_modules/,
        loaders: ['babel-loader'],
      },
      {
        test: /\.(jpe?g|png|gif|svg|ttf|eot|woff|woff2)$/i,
        exclude: /node_modules/,
        loaders: ['url?limit=8192', 'img',
                ],
      },
      {
        test: /\.json$/,
        loader: 'json',
      },
    ],
  },
  plugins: [
    new ExtractTextPlugin('./styles.css', {
      allChunks: true,
    }),
    new webpack.HotModuleReplacementPlugin(),
  ],
  resolve: {
    extensions: [
      '',
      '.js',
      '.jsx',
      '.json',
    ],
  },
  devServer: {
    historyApiFallback: true,
    contentBase: './',
  },
};

module.exports = config;
