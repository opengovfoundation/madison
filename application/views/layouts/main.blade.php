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
		<div class="header">
			<h1><a href="{{ URL::to('/') }}">Madison Federal</a></h1>
			<h2>Collaborate With Congress</h2>
			<div class="nav">
				<ul>
					<li><a href="{{ URL::to('about') }}">About the Madison Platform</a></li>
					<li><a href="{{ URL::to('faq') }}">FAQ</a></li>
					<li>
						<a href="#">User Name</a>
						<ul>
							<li><a href="#">Bookmarked Bills</a></li>
							<li><a href="#">Your Points</a></li>
							<li><a href="#">Account Settings</a></li>
							<li><a href="#">Help</a></li>
							<li><a href="#">Logout</a></li>
						</ul>
					</li>
				</ul>
			</div>
			<form class="search-form" action="" method="post">
				<input type="search" placeholder="Search" value="" />
				<input type="submit" value="Go" />
			</form>
		</div>

		<div id="main">
			@yield('content')
		</div>

		<div class="footer">
			<div class="nav">
				<ul>
					<li><a href="#">The OpenGov Foundation</a></li>
					<li><a href="#">Media Inquiries</a></li>
					<li><a href="#">Contact</a></li>
					<li><a href="#">Terms &amp; Conditions</a></li>
					<li><a href="#">Report a Bug</a></li>
				</ul>
			</div>
			<p>From the Office of Congressman <a href="#">Darrell Issa</a></p>
		</div>
	</body>
</html>