@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>Login</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('url'=>'user/login', 'method'=>'post')) }}
				<!-- Email -->
				<div class="form-group">
					{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email', 'class'=>'form-control')) }}
				</div>
				<!-- Password -->
				<div class="form-group">
					{{ Form::label('password', 'Password') . Form::password('password', array('placeholder'=>'Password', 'class'=>'form-control')) }}
				</div>
				<!-- Remember checkbox -->
				<div class="checkbox">
					{{ Form::label('remember', 'Remember me') . Form::checkbox('remember', 'true') }}
				</div>
				<!-- Submit -->
				{{ Form::submit('Login', array('class'=>'btn btn-default')) }}
				<a class="forgot-password" href="{{ URL::to('password/remind') }}">Forgot your password?</a>
				<a class="forgot-password" href="{{ URL::to('verification/remind') }}">Resend confirmation email</a>
				{{ Form::hidden('previous_page', $previous_page) }}
				{{ Form::token() . Form::close() }}
			</div>
		</div>
		<div class="row">
			<div social-login message="Log in"></div>
		</div>
	</div>
@endsection
