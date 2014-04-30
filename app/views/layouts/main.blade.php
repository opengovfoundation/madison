<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" id="ng-app" ng-app="madisonApp" lang="en">
	<head>
		<meta charset="utf-8" />
		@if(isset($page_title)) 
		<title>{{ $page_title }}</title>
		@endif
		<!-- Mobile Optimization -->
		<meta name="HandheldFriendly" content="True" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimum-scale=1.0">
		<meta name="format-detection" content="telephone=no" />
		<meta http-equiv="cleartype" content="on" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		@include('layouts.socials')
		@include('layouts.assets')
		<!--[if lt IE 9]>
		<script>
		window.console = window.console || {};
		window.console.log = window.console.log || function () {};

		$(document).ready(function () {
			angular.bootstrap(document);
		});
		</script>
		<![endif]-->
	</head>
	<body>
		<!--[if lt IE 8]>
			<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" target="_blank">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->
		<div id="wrap" class="wrap">
			<div id="header-main" class="header row">
				<div class="container">
				@include('layouts.header')
				</div>
			</div>
			<div class="row">
				<div class="container">
					<div class="md-col-12">&nbsp;</div>
				</div>
			</div>
			<div class="row">
				<div class="container alerts">
					@include('errors')
					@include('message')
					@include('success')
				</div>
			</div>

			<div id="main" class="row">
				<div class="container">
					<div class="row">
						@yield('content')
					</div>
				</div>
			</div>
		</div>
		<div id="footer" class="footer row">
			@include('layouts.footer')
		</div>
	</body>
</html>
