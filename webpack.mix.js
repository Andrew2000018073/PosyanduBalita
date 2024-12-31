const mix = require('laravel-mix');
const webpack = require('webpack');


mix.js('resources/js/app.js', 'public/js')
   .vue()
   .webpackConfig({
       // Define Vue feature flags
       plugins: [
           new webpack.DefinePlugin({
               __VUE_OPTIONS_API__: JSON.stringify(true),
               __VUE_PROD_DEVTOOLS__: JSON.stringify(false),
               __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: JSON.stringify(false),
           })
       ]
   })
   .postCss('resources/css/app.css', 'public/css')
   .autoload({
    jquery: ['$', 'window.jQuery', 'jQuery'],
})
.sourceMaps();

