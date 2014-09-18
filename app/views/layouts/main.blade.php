<!DOCTYPE html>
<html xmlns:ng="http://angularjs.org" id="ng-app" ng-app="madisonApp"  ng-controller="AppController" lang="en">
	<head>
		<meta charset="utf-8" />
		@if(isset($page_title))
		<title>{{ $page_title }}</title>
		@else
		<title ng-bind="pageTitle">Madison</title>
		@endif
		<!-- Mobile Optimization -->
		<meta name="HandheldFriendly" content="True" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimum-scale=1.0">
		<meta name="format-detection" content="telephone=no" />
		<meta http-equiv="cleartype" content="on" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<!--[if lt IE 9]>
		<script>
          document.createElement('ng-include');
          document.createElement('ng-pluralize');
          document.createElement('ng-view');

          // Optionally these for CSS
          document.createElement('ng:include');
          document.createElement('ng:pluralize');
          document.createElement('ng:view');
		</script>
		<![endif]-->
		@include('layouts.socials')
		@include('layouts.assets')
	</head>
	<body>
		<!--[if lt IE 8]>
			<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/" target="_blank">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->
		<tour step="currentStep" post-tour="tourComplete()" post-step="stepComplete()" ng-controller="TourController">
			<div growl></div>
			<div id="wrap">
				<div id="header-main" class="navbar" role="navigation">
					<div class="container">
					@include('layouts.header')
					</div>
				</div>
				<div class="row">
					<div profile-completion-message></div>
				</div>
				<div class="row">
					<div class="container alerts">
						@include('errors')
						@include('message')
						@include('success')
					</div>
				</div>
				<div id="main" class="">
					<div class="container">
						@yield('content')
					</div>
				</div>
			</div>
			<div id="footer" class="footer">
				@include('layouts.footer')
			</div>
		</tour>
	</body>
</html>
