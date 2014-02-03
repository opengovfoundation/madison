<nav class="navbar navbar-default" role="navigation">
	@if(!$docs->isEmpty())
	<ul class="nav navbar-nav">
		<li class="dropdown">
			<a href="#" class="dropdown-toggle black" data-toggle="dropdown">Select a recent document<b class="caret"></b></a>
			<ul class="dropdown-menu">
				<li><a href="{{ URL::to('docs') }}">View All</a></li>
				@foreach($docs as $doc)
					<li><a href="{{ URL::to('docs/' . $doc->slug) }}">{{ $doc->title }}</a></li>
				@endforeach
	        </ul>
		</li>
	</ul>
	@endif
	<form class="navbar-form navbar-right hidden" role="search">
		<div class="form-group">
			<input type="text" class="form-control disabled coming-feature" placeholder="Search this bill" disabled>
		</div>
		<button type="submit" class="btn btn-default" disabled>Submit</button>
	</form>
</nav>
