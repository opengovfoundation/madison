<div class="logo-madison col-md-3 col-md-offset-1">
	<a class="link-home" href="{{ URL::to('/') }}">
		<h1 class="blue single-shadow">Madison <span class="level">Federal</span></h1>
		<h2 class="blue small-header">&#149; Collaborate With Congress &#149;</h2>
	</a>
</div>

<div class="nav nav-main col-md-4">

	<ul>
		<li class="link-about"><a href="{{ URL::to('about') }}">About the Madison Platform</a></li>
		<li class="link-faq"><a href="{{ URL::to('faq') }}">FAQ</a></li>		
		@if(Auth::check())
			<li class="dropdown">
				<a class="dropdown-trigger" href="#" data-toggle="dropdown">Welcome {{-- Auth::user()->fname --}} <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li class="link-bookmarked"><a href="#" class="disabled coming-feature">Bookmarked Bills</a></li>
					<li class="link-points"><a href="#" class="disabled coming-feature">Your Points</a></li>
					<li class="link-settings"><a href="#" class="disabled coming-feature">Account Settings</a></li>
					<li class="link-help"><a href="#" class="disabled coming-feature">Help</a></li>
					<li class="link-logout"><a href="{{ URL::to('logout') }}">Logout</a></li>
				</ul>
			</li>
		@else
			<li class="link-login"><a href="{{ URL::to('login') }}">Login</a></li>
			<li class="link-signup"><a href="{{ URL::to('signup') }}">Sign Up</a></li>
		@endif
	</ul>

</div>

<div class="global-search col-md-4">
	<form action="" method="post">
		<input type="search" class="input-search form-control dark-search disabled coming-feature" placeholder="Search" value="" />
		<input class="input-submit" type="submit" value="Search" />
	</form>
</div>