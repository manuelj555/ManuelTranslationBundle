const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('./public/')
    .setPublicPath('./')
    .setManifestKeyPrefix('bundles/manueltranslation')
    .addEntry('app', './assets/js/app.js')
    .addEntry('config', './assets/js/config/index.js')
    .addEntry('conflicts', './assets/js/conflicts/index.js')
    .addEntry('profiler', './assets/js/profiler/index.js')
    .addStyleEntry('profiler_css', './assets/css/profiler.scss')
    .addStyleEntry('styles', './assets/css/app.scss')
    .disableSingleRuntimeChunk()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })
    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })
    .enableSassLoader()
    .enableReactPreset()
;

if (Encore.isProduction()) {
    Encore.cleanupOutputBeforeBuild()
}

module.exports = Encore.getWebpackConfig();