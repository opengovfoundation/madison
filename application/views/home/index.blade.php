@layout('layouts/main')
@section('content')
<div class="row-fluid">
	<div class="span12">
		<h3>Welcome to the <strong>Madison Federal</strong> editing page</h3>
		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.  Fusce in eros vel nibh hendrerit elementum nec id magna.  Aliquam sed elementum tellus.  Fusce vulputate pretium nisi in accumsan.  Donec tempus ornare enim.</p>
		<h4>Ready to get started?</h4>
		<p><a href="{{ URL::to('signup') }}">Signup</a> or <a href="{{ URL::to('login') }}">Login</a> if you already have an account.</p>
	</div>
</div>
@endsection