<!-- Polyfills -->
<!--[if lt IE 9]>
<script src="/polyfills/es5.js"></script>
<script src="/polyfills/eventListener.js"></script>
<script src="/polyfills/html5shiv.js"></script>
<![endif]-->


<!-- Stylesheets -->
{{ HTML::style('build/app.css') }}

<!-- Scripts -->
{{ HTML::script('build/app.js') }}

<?php
$fs = new Illuminate\Filesystem\Filesystem();
?>
{{-- Include site-specific uservoice js file if it exists --}}
@if($fs->exists(public_path() . '/js/uservoice.js'))
	{{ HTML::script('js/uservoice.js') }}
@endif

{{-- Include site-specific google analytics js file if it exists --}}
@if($fs->exists(public_path() . '/js/ga.js'))
    {{ HTML::script('js/ga.js') }}
@endif




