<!-- Stylesheets -->
{{ HTML::style('vendor/jquery/jquery-ui-smoothness.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap.min.css') }}
{{ HTML::style('vendor/bootstrap/css/bootstrap-theme.min.css') }}
{{ HTML::style('css/style.css') }}

<!-- Scripts -->
{{ HTML::script('vendor/jquery/jquery-1.10.2.js') }}
{{ HTML::script('vendor/jquery/jquery.browser.min.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.widget.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.mouse.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.sortable.js') }}
{{ HTML::script('vendor/jquery/jquery.mjs.nestedSortable.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.autocomplete.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.core.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.menu.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.position.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.effect.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.effect-blind.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.effect-highlight.js') }}
{{ HTML::script('vendor/jquery/jquery.ui.effect-forecolor-highlight.js') }}
{{ HTML::script('vendor/bootstrap/js/bootstrap.min.js') }}
{{ HTML::script('vendor/modernizr-latest.js') }}
{{ HTML::script('vendor/underscore.min.js') }}
{{ HTML::script('vendor/pagedown/assets/Markdown.Converter.js') }}
{{ HTML::script('vendor/angular/angular.min.js') }}
{{ HTML::script('js/angular/app.js') }}
{{ HTML::script('js/angular/controllers.js') }}
{{ HTML::script('js/angular/services.js') }}
{{ HTML::script('js/angular/directives.js') }}
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




