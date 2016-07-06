var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var baseConfig = require('./webpack.base.config.js');
var StageDependancies = require('./stageDependancies');
var buildPath = ['../htdocs/public/build/', require('../package.json').name].join('');

var config = {
  entry: [ './index.js' ],
  output: {
    filename: 'bundle.js',
    publicPath: '/build',
    path: buildPath,
  },
  plugins: [
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
  ]
};

var output = Object.assign({}, baseConfig, {output: config.output});
output.entry  = output.entry.concat(config.entry);
output.plugins = output.plugins.concat(config.plugins);


module.exports = output;