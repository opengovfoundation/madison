<!-- Stylesheets -->
{{ HTML::style('vendor/datetimepicker/datetimepicker.css') }}
{{ HTML::style('vendor/jquery/jquery-ui-smoothness.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap-theme.min.css') }}
{{ HTML::style('vendor/select2/select2.css') }}
{{ HTML::style('css/style.css') }}

<!-- Scripts -->
{{ HTML::script('vendor/jquery/jquery-1.10.2.min.js') }}
{{ HTML::script('vendor/jquery/jquery.browser.min.js') }}
{{ HTML::script('vendor/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('vendor/modernizr-latest.js') }}
{{ HTML::script('vendor/underscore.min.js') }}
{{ HTML::script('vendor/pagedown/assets/Markdown.Converter.js') }}
{{ HTML::script('vendor/angular/angular.min.js') }}
{{ HTML::script('vendor/angular/angular-animate.js') }}
{{ HTML::script('vendor/angular-md5.js') }}
{{ HTML::script('vendor/ui-bootstrap-tpls-0.10.0.min.js') }}
{{ HTML::script('vendor/ui-utils.min.js') }}
{{ HTML::script('vendor/select2/select2.js') }}
{{ HTML::script('vendor/select2/ui-select2.js') }}
{{ HTML::script('vendor/moment.js') }}
{{ HTML::script('vendor/datetimepicker/datetimepicker.js') }}
{{ HTML::script('js/angular/app.js') }}
{{ HTML::script('js/angular/controllers.js') }}
{{ HTML::script('js/angular/services.js') }}
{{ HTML::script('js/angular/directives.js') }}
{{ HTML::script('js/angular/filters.js') }}
{{ HTML::script('js/madison.js') }}

<?php 
$fs = new Illuminate\Filesystem\Filesystem();
?>
{{-- Include site-specific uservoice js file if it exists --}}
@if($fs->exists(public_path() . '/js/uservoice.js'))
	{{ HTML::script('js/uservoice.js') }}
@endif

{{-- Include site-specific addthis js file if it exists --}}
@if($fs->exists(public_path() . '/js/addthis.js'))
	{{ HTML::script('js/addthis.js') }}
@endif

{{-- Include site-specific google analytics js file if it exists --}}
@if($fs->exists(public_path() . '/js/ga.js'))
    {{ HTML::script('js/ga.js') }}
@endif




