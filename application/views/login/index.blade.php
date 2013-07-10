@layout('layouts.main')

@section('content')
	<h1>Login</h1>
	{{ Form::open('login', 'post') }}
	{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email')) }}
	{{ Form::label('password', 'Password') . Form::password('password', array('placeholder'=>'Password')) }}
	{{ Form::submit('Login') }}
	{{ Form::token() . Form::close() }}
@endsection