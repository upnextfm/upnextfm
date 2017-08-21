// webpack.config.js
var path = require('path');
var Encore = require('@symfony/webpack-encore');
var webpack = require('webpack');
var BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

Encore
  .setOutputPath('./web/build/')
  .setPublicPath('/build')

  .addEntry('js/app', './web/src/js/app.jsx')
  .addEntry('js/room', './web/src/js/room.jsx')
  .addStyleEntry('css/app', './web/src/scss/app.scss')
  .addStyleEntry('css/materialize', './web/src/scss/materialize.scss')
  .enableReactPreset()
  .enableSassLoader()
  .enablePostCssLoader()

  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
;

let config = Encore.getWebpackConfig();
//config.plugins.push(new BundleAnalyzerPlugin());

module.exports = config;
