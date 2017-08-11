// webpack.config.js
var path = require('path');
var Encore = require('@symfony/webpack-encore');
var webpack = require('webpack');
var BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

Encore
  .setOutputPath('./web/build/')
  .setPublicPath('/build')

  .addEntry('js/app', './web/src/js/app.jsx')
  .addStyleEntry('css/app', './web/src/scss/app.scss')
  .enableReactPreset()
  .enableSassLoader()
  .enablePostCssLoader()

  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
;

let config = Encore.getWebpackConfig();

// Cuts down on the size of highlight.js.
config.plugins.push(new webpack.ContextReplacementPlugin(
  /highlight\.js\/lib\/languages$/,
  new RegExp(`^./(${['javascript', 'php', 'bash', 'yaml', 'xml', 'twig', 'java'].join('|')})$`)
));
//config.plugins.push(new BundleAnalyzerPlugin());

// export the final configuration
module.exports = config;
