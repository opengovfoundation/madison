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
       .webpack('app.js')
       .combine(
         [
          'node_modules/jquery/dist/jquery.min.js',
          'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
         ],
         'public/js/vendor.js'
       )
       .copy('resources/assets/img', 'public/img')
       .version(['css/app.css', 'js/app.js', 'js/vendor.js'])
  ;
});
