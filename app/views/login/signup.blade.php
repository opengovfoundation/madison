@extends('layouts/main')


@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>{{ trans('signup.signup') }}</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('url'=>'user/signup', 'method'=>'post')) }}
				<!-- First Name -->
				<div class="form-group">
					{{ Form::label('fname', Lang::get('signup.fname')) . Form::text('fname', Input::old('fname'), array('placeholder'=>Lang::get('signup.fname'), 'class'=>'form-control')) }}
				</div>
				<!-- Last Name -->
				<div class="form-group">
					{{ Form::label('lname', Lang::get('signup.lname')) . Form::text('lname', Input::old('lname'), array('placeholder'=>Lang::get('signup.lname'), 'class'=>'form-control')) }}
				</div>
				<!-- Email -->
				<div class="form-group">
					{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email', 'class'=>'form-control')) }}
				</div>
				<!-- Password -->
				<div class="form-group">
					{{ Form::label('password', Lang::get('index.password') ) . Form::password('password', array('placeholder'=>Lang::get('index.password'), 'class'=>'form-control')) }}
				</div>
				<!-- Submit -->
				{{ Form::submit(Lang::get('signup.signup'), array('class'=>'btn btn-default')) }}
				{{ Form::token() . Form::close() }}
			</div>
		</div>
		<div class="row">
			<div social-login message="{{ trans('signup.signup') }}"></div>
		</div>
	</div>
@endsection