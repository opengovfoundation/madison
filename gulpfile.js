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

elixir.config.css.autoprefix.options.browsers = ['> 1%', 'last 2 versions', 'Firefox ESR', 'IE >= 10'];
elixir.config.css.autoprefix.options.remove = false;

elixir((mix) => {
    mix.sass('app.scss')
       .sass('mail.scss', 'resources/views/vendor/mail/html/themes/opengov.css')
       .webpack('annotator-madison.js')
       .webpack('app.js')
       .webpack('document.js')
       .webpack('document-edit.js')
       .combine(
         [
          'node_modules/jquery/dist/jquery.min.js',
          'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
          'node_modules/select2/dist/js/select2.min.js',
          'resources/assets/vendor/js/annotator-full.min.js',
          'resources/assets/vendor/js/modernizr-custom.js',
          'node_modules/simplemde/dist/simplemde.min.js',
         ],
         'public/js/vendor.js'
       )
       .copy('resources/assets/img', 'public/img')
       .copy('resources/assets/icons', 'public/')
       .copy('node_modules/font-awesome/fonts/', 'public/fonts/')
       .version([
         'css/app.css',
         'js/annotator-madison.js',
         'js/app.js',
         'js/document.js',
         'js/document-edit.js',
         'js/vendor.js'
       ])
  ;
});
