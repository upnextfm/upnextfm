// webpack.config.js
var path = require('path');
var Encore = require('@symfony/webpack-encore');
var webpack = require('webpack');
var BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
var LiveReloadPlugin = require('webpack-livereload-plugin');

Encore
  .setOutputPath('./web/build/')
  .setPublicPath('/build')

  .addEntry('js/app', './web/src/js/app.jsx')
  .addEntry('js/room', './web/src/js/room.jsx')
  .addEntry('js/admin', './web/src/js/admin.jsx')
  .addStyleEntry('css/app', './web/src/scss/app.scss')
  .addStyleEntry('css/admin', './web/src/scss/admin.scss')
  .addStyleEntry('css/loader', './web/src/scss/loader.scss')
  .addStyleEntry('css/materialize', './web/src/scss/materialize.scss')
  .enableReactPreset()
  .enableSassLoader()
  .enablePostCssLoader()

  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
;

let config = Encore.getWebpackConfig();
//config.plugins.push(new BundleAnalyzerPlugin());
config.plugins.push(new webpack.DefinePlugin({
  'PRODUCTION': JSON.stringify(Encore.isProduction())
}));
config.plugins.push(new LiveReloadPlugin({ port: 35730 }));

module.exports = config;
