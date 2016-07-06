var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var StageDependancies = require('./stageDependancies');
var buildPath = ['../htdocs/public/build/', require('../package.json').name].join('');

var config = {
  entry: [ './index.js' ],
  output: {
    filename: 'bundle.js',
    publicPath: '/build',
    path: buildPath,
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
    new webpack.DefinePlugin({
      'process.env':{
        'NODE_ENV': JSON.stringify('production')
      }
    }),
    new webpack.optimize.UglifyJsPlugin({
      compress:{
        warnings: true
      }
    }),
    new StageDependancies( {appendTo:'.terminal', default: true, manifestFile: [buildPath, 'manifest.json'].join('/') })
  ],
  resolve: {
    extensions: [
      '',
      '.js',
      '.jsx',
      '.json',
    ],
  }
};

module.exports = config;
