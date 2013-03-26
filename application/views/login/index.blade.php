@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<h1>Login</h1>
<div class="row-fluid well well-large">
	<div class="span12">
		{{ Form::open('login', 'post') }}
		{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email')) }}
		{{ Form::label('password', 'Password') . Form::password('password', array('placeholder'=>'Password')) }}
		{{ Form::submit('Login') }}
		{{ Form::token() . Form::close() }}
	</div>
</div>
@endsection