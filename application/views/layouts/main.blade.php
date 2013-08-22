<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Madison Federal &mdash; Collaborate With Congress</title>
		<!-- Mobile Optimization -->
		<meta name="HandheldFriendly" content="True" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimum-scale=1.0">
		<meta name="format-detection" content="telephone=no" />
		<meta http-equiv="cleartype" content="on" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
		<!-- Stylesheets -->
		{{ Asset::styles() }}
		<!-- Scripts -->
		{{ Asset::scripts() }}
	</head>
	<body>
		<div id="header" class="header row">
			<div class="container">
				<div class="row">
					@include('layouts.header')
				</div>
			</div>
		</div>
		
		<div class="container">
			<div id="topbar-wrapper" class="row">
				<div class="col-md-12 topbar">
					@include('partials.topbar')
				</div>
			</div>
		</div>
		
		<div id="main" class="container">
			<div class="row">
				@yield('content')
			</div>
		</div>
		
		<div class="footer row">
			<div class="container">
				<div class="row">
					@include('layouts.footer')
				</div>
			</div>
		</div>
	</body>
</html>