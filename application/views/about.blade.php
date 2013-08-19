@layout('layouts.main')

@section('content')
	<div class="span2 spacer"></div>
	<div class="content span6">
		<h1>About the Madison Platform</h1>
		<p>About content. Lorem ipsum dolor sit amet.</p>
		@include('partials.learn')
	</div>
	<div class="span4 spacer"></div>
@endsection