@layout('layouts.main')

@section('content')
	<div class="span2 spacer"></div>
	<div class="content span6">
		<h1>Welcome to the <strong>Madison Federal</strong> editing page</h1>
		<p>Home page content. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce in eros vel nibh hendrerit elementum nec id magna.  Aliquam sed elementum tellus. Fusce vulputate pretium nisi in accumsan. Donec tempus ornare enim.</p>
		<h2>Ready to get started?</h2>
		<p><a href="{{ URL::to('signup') }}">Signup</a> or <a href="{{ URL::to('login') }}">Login</a> if you already have an account.</p>
	</div>
	@include('partials.rightbar.getstarted')
	<div class="span2 spacer"></div>
@endsection