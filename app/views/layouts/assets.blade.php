<!-- Stylesheets -->
{{ HTML::style('vendor/datetimepicker/datetimepicker.css') }}
{{ HTML::style('vendor/jquery/jquery-ui-smoothness.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap-theme.min.css') }}
{{ HTML::style('vendor/select2/select2.css') }}
{{ HTML::style('css/style.css') }}

<!-- Scripts -->
{{ HTML::script('build/app.js') }}
{{-- HTML::script('build/modules.js') --}}
{{-- HTML::script('js/angular/app.js') --}}
{{-- HTML::script('js/angular/controllers.js') --}}
{{-- HTML::script('js/angular/services.js') --}}
{{-- HTML::script('js/angular/directives.js') --}}
{{-- HTML::script('js/angular/filters.js') --}}
{{-- HTML::script('js/madison.js') --}}

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




