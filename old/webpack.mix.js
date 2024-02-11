const mix = require('laravel-mix');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
//const fs = require("fs");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

const webpackConfig = {
    plugins: [
        new CleanWebpackPlugin(), // Cleans output path after build
    ],
};

mix.js('resources/js/app.js', 'public/js').version();
mix.js('resources/js/app.debug.js', 'public/js').version();
mix.js('resources/js/pages/Explorer.js', 'public/js/pages/explorer.js').version();
mix.js('resources/js/pages/NodeStats.js', 'public/js/pages/stats/nodes.js').version();
mix.js('resources/js/pages/AcceleratorStats.js', 'public/js/pages/stats/accelerator.js').version();
mix.sass('resources/scss/app.scss', 'public/css').version();
mix.copyDirectory('resources/svg', 'public/svg')
    .copyDirectory('resources/images', 'public/img')
    .copyDirectory('resources/favicon', 'public')
    .copyDirectory('node_modules/bootstrap-icons/icons', 'public/svg/icons');

// fs.readdirSync("resources/js/pages")
//     .forEach(fileName => mix.js(`resources/js/pages/${fileName}`, "public/js/pages"));
