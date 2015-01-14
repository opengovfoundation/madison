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
		<tour step="currentStep" post-tour="tourComplete()" post-step="stepComplete()">
			<div growl></div>
			@include('layouts.header')
			@include('errors')
			@include('message')
			@include('success')
			
			<div profile-completion-message></div>

			<div id="wrap">
				<div id="main" class="">
					@yield('content')
				</div>
			</div>
			<footer class="main-footer">
			  <div class="container">
			    <div class="row">
			      <div class="col-sm-4">
			        <img class="gobmx-footer" src="/svg/gob-mx-logo.svg" width="126" height="39" alt="gob.mx">
			      </div>
			      <div class="col-sm-4 text-center">
			        <img src="/svg/mover-mexico-logo.svg" width="172" height="70" alt="gob.mx">
			      </div>
			      <div class="col-sm-4 text-right">
			      	<p>Insurgentes Sur 1735, Col. Guadalupe Inn. Delegación Álvaro Obregón México, D.F. C.P. 01020 </p>
			      </div>
			    </div>
			  </div>
			</footer>
		</tour>
	</body>
</html>
