<!-- Polyfills -->
<!--[if lt IE 9]>
<script src="/polyfills/es5.js"></script>
<script src="/polyfills/eventListener.js"></script>
<script src="/polyfills/html5shiv.js"></script>
<![endif]-->
 

<!-- Stylesheets -->
{{ HTML::style('vendor/pagedown/assets/demo.css') }}
{{ HTML::style('vendor/datetimepicker/datetimepicker.css') }}
{{ HTML::style('vendor/jquery/jquery-ui-smoothness.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap-theme.min.css') }}
{{ HTML::style('vendor/select2/select2.css') }}
{{ HTML::style('css/style.css') }}

<!-- Scripts -->
{{ HTML::script('bower_components/pagedown/Markdown.Converter.js') }}
{{ HTML::script('bower_components/pagedown/Markdown.Sanitizer.js') }}
{{ HTML::script('bower_components/pagedown/Markdown.Editor.js') }}
{{ HTML::script('build/app.js') }}

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




