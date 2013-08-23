<div class="col-md-4 col-md-offset-1">
	<h1 class="blue single-shadow"><a href="{{ URL::to('/') }}">Madison Federal</a></h1>
	<h2 class="blue small-header">Collaborate With Congress</h2>
</div>
<div class="nav col-md-7">
	<ul>
		<li><a href="{{ URL::to('about') }}">About Madison</a></li>
		<li><a href="{{ URL::to('faq') }}">FAQ</a></li>
		<li>
			@if(Auth::check())
			<div class="dropdown">
				<a href="#" data-toggle="dropdown">Welcome {{ Auth::user()->fname }} <span class="caret"></span></a>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#">Bookmarked Bills</a></li>
					<li><a href="#">Your Points</a></li>
					<li><a href="#">Account Settings</a></li>
					<li><a href="#">Help</a></li>
					<li><a href="{{ URL::to('logout') }}">Logout</a></li>
				</ul>
			</div>
			@else
			<a href="{{ URL::to('login') }}">Login</a>
			<a href="{{ URL::to('signup') }}">Sign Up</a>
			@endif
		</li>
		<li>
			<form action="" class="search-form" method="post">
				<input type="search" class="dark-search disabled coming-feature" placeholder="Search" value="" />
				<!-- <input type="submit" value="Go" /> -->
			</form>
		</li>
	</ul>
</div>