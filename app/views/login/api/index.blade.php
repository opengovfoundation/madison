{{-- 
<div class="row">
	<div class="md-col-12">
		<h1>Login</h1>
	</div>
</div>
--}}

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		{{ Form::open(array('url'=>'api/user/login', 'method'=>'post')) }}
		<div class="errors"></div>
		<!-- Email -->
		<div class="form-group">
			{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email', 'class'=>'form-control')) }}
		</div>
		<!-- Password -->
		<div class="form-group">
			{{ Form::label('password', 'Password') . Form::password('password', array('placeholder'=>'Password', 'class'=>'form-control')) }}
		</div>
		<!-- Submit -->
		{{ Form::submit('Login', array('class'=>'btn btn-default')) }}
		<a class="forgot-password" href="{{ URL::to('password/remind') }}">Forgot your password?</a>
		{{ Form::token() . Form::close() }}
	</div>
</div>
