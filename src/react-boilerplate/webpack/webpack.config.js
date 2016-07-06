var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var baseConfig = require('./webpack.base.config.js');

var config = {
  entry: [
    'webpack-dev-server/client?http://localhost:3000',
    'webpack/hot/only-dev-server',
    'react-hot-loader/patch'
  ],
  output: {
    filename: 'bundle-dev.js',
    publicPath: '/public',
    path: './server/build',
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env':{
        'NODE_ENV': JSON.stringify('development')
      }
    }),
    new webpack.HotModuleReplacementPlugin()
  ],
  devServer: {
    historyApiFallback: true,
    contentBase: './server',
  },
};

var output = Object.assign({}, baseConfig, {output: config.output, devServer: config.devServer});
output.entry  = output.entry.concat(config.entry);
output.plugins = output.plugins.concat(config.plugins);

module.exports = output;