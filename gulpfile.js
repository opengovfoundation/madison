const elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir((mix) => {
    mix.sass('app.scss')
       .webpack('annotator-madison.js')
       .webpack('app.js')
       .webpack('document.js')
       .combine(
         [
          'node_modules/jquery/dist/jquery.min.js',
          'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
          'node_modules/select2/dist/js/select2.min.js',
          'resources/assets/vendor/js/annotator-full.min.js',
         ],
         'public/js/vendor.js'
       )
       .copy('resources/assets/img', 'public/img')
       .copy('resources/assets/icons', 'public/')
       .copy('resources/assets/vendor/css/annotator.min.css', 'public/css/')
       .copy('node_modules/font-awesome/fonts/', 'public/fonts/')
       .version([
         'css/app.css',
         'js/annotator-madison.js',
         'js/app.js',
         'js/document.js',
         'js/vendor.js'
       ])
  ;
});
