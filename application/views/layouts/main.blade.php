<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>Madison Federal &mdash; Collaborate With Congress</title>
		<!-- Mobile Optimization -->
		<meta name="HandheldFriendly" content="True" />
		<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no" />
		<meta name="format-detection" content="telephone=no" />
		<meta http-equiv="cleartype" content="on" />
		<!-- Stylesheets -->
		{{ Asset::styles() }}
		<!-- Scripts -->
		{{ Asset::scripts() }}
	</head>
	<body>
		<div id="header" class="header row-fluid">
			<div class="span1 spacer"></div>
			<div class="span4">
				<h1 class="blue single-shadow"><a href="{{ URL::to('/') }}">Madison Federal</a></h1>
				<h2 class="blue small-header">Collaborate With Congress</h2>
			</div>
			<div class="nav span7">
				<ul>
					<li><a href="{{ URL::to('about') }}">About the Madison Platform</a></li>
					<li><a href="{{ URL::to('faq') }}">FAQ</a></li>
					<li>
						<!-- TODO:  Check if user's signed in -->
						<a href="#">User Name</a>
						<ul class="dropdown">
							<li><a href="#">Bookmarked Bills</a></li>
							<li><a href="#">Your Points</a></li>
							<li><a href="#">Account Settings</a></li>
							<li><a href="#">Help</a></li>
							<li><a href="#">Logout</a></li>
						</ul>
					</li>
					<li>
						<form action="" class="search-form" method="post">
							<input type="search" class="dark-search" placeholder="Search" value="" />
							<!-- <input type="submit" value="Go" /> -->
						</form>
					</li>
				</ul>
			</div>
		</div>
		<div id="topbar-wrapper" class="row-fluid">
			<div class="span2 spacer"></div>
			<div class="span9 topbar">
				@include('partials.topbar')
			</div>
			<div class="span1 spacer"></div>
		</div>
		<div id="main" class="row-fluid">
			@yield('content')
		</div>

		<div class="footer row-fluid">
			<div class="span2 spacer"></div>
			<div class="nav span10">
				<ul>
					<li><a href="#">The OpenGov Foundation</a></li>
					<li><a href="#">Media Inquiries</a></li>
					<li><a href="#">Contact</a></li>
					<li><a href="#">Terms &amp; Conditions</a></li>
					<li><a href="#">Report a Bug</a></li>
				</ul>
			</div>
			<div class="span2 spacer"></div>
		</div>
	</body>
</html>