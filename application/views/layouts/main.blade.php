<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		{{ Asset::styles() }}
		{{ Asset::scripts() }}
	</head>
	<body>
		<div id="main" class="container-fluid">
			<div id="header" class="row-fluid">
				<div class="span1 spacer"></div>
				<div class="span4">
					<h1 class="blue single-shadow">Madison</h1>
					<p>Collaborate With Congress</p>
				</div>
				<div class="span4">
					<a href="#">About the Madison Platform</a>
					<a href="#">FAQ</a>
					<a href="{{ URL::to('login') }}">Login</a>
					<a href="{{ URL::to('signup') }}">Sign Up</a>
				</div>
				<div class="span3">
					<input type="text" placeholder="Search"/>
				</div>
			</div>
			<div id="navigation-wrapper" class="row-fluid">
				<div class="span2 spacer"></div>
				<div id="navigation" class="span9">
					<div class="row-fluid">
						<div class="span3">
							<select id="doc-nav">
								<option value="">Select a recent bill</option>
								@foreach($docs as $doc)
								<option value="{{ URL::to('doc/' . $doc->slug) }}">{{$doc->title}}</option>
								@endforeach
							</select>
						</div>
						<div class="span3">
							<input type="button" class="btn btn-inverse" value="Request a Bill"/>
							<a href="#">Advanced Bill Search &gt;&gt;</a>
						</div>
						<div class="span3">
							<input type="text" placeholder="Search this bill"/>
						</div>
					</div>
				</div>
			</div>
			<div id="alert-wrapper" class="row-fluid">
				@render('success')
				@render('message')
				@render('errors')
			</div>
    		<div id="content" class="row-fluid">
				<div class="span1 spacer"></div>
				<div id="leftbar" class="span1">
					<ul>
						<li>Draft</li>
						<li>Introduced</li>
						<li>Markup</li>
						<li>House</li>
						<li>Senate</li>
						<li>Final</li>
						<li>Review</li>
						<li>Bill Overview</li>
					</ul>
				</div>
				<div id="page" class="span6">
					@section('content')
					Content Area
					@yield_section
				</div>
				<div id="rightbar" class="span3">
					<h2>Participate</h2>
				</div>
			</div>
			<div id="main-footer"></div>
		</div>
		<div id="footer" class="container-fluid">
			<div class="span8 offset2">
				<a href="">THE OPENGOV FOUNDATION</a>
				<a href="">MEDIA INQUIRIES</a>
				<a href="">CONTACT</a>
				<a href="">TERMS &amp; CONDITIONS</a>
				<a href="">REPORT A BUG</a>
			</div>
  		</div>
	</body>
</html>