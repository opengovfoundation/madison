@extends('layouts.main')
@section('content')
	<div class="container">
		<div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3">
			<div class="content">
				<h1>{{ trans('messages.login') }}</h1>
				{{ Form::open(array('url'=>'user/login', 'method'=>'post')) }}
				<!-- Email -->
				<div class="form-group">
					{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email', 'class'=>'form-control')) }}
				</div>
				<!-- Password -->
				<div class="form-group">
					{{ Form::label('password', Lang::get('messages.password') ) . Form::password('password', array('placeholder'=>Lang::get('messages.password'), 'class'=>'form-control')) }}
				</div>
				<!-- Remember checkbox -->
				<div class="checkbox">
					{{ Form::label('remember', Lang::get('messages.rememberme') ) . Form::checkbox('remember', 'true') }}
				</div>
				<!-- Submit -->
				{{ Form::submit(Lang::get('messages.login'), array('class'=>'btn btn-default')) }}
				<br><br>
				<ul class="list-unstyled">
					<li>
						<a class="forgot-password" href="{{ URL::to('password/remind') }}">{{ trans('messages.forgotpassword') }}</a>
					</li>
					<li>
						<a class="forgot-password" href="{{ URL::to('verification/remind') }}">{{ trans('messages.resend') }}</a>
					</li>
					<li>
						<a class="forgot-password" href="{{ URL::to('user/signup') }}" target="_self">{{ trans('messages.signup') }}</a>
					</li>
				</ul>

				{{ Form::hidden('previous_page', $previous_page) }}
				{{ Form::token() . Form::close() }}
			</div>
			<div class="row">
				<div social-login message="{{ trans('messages.sociallogin') }}"></div>
			</div>
		</div>
	</div>
@endsection
