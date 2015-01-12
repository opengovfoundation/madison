@extends('layouts/main')


@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>{{ trans('messages.signup') }}</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('url'=>'user/signup', 'method'=>'post')) }}
				<!-- First Name -->
				<div class="form-group">
					{{ Form::label('fname', Lang::get('messages.fname')) . Form::text('fname', Input::old('fname'), array('placeholder'=>Lang::get('messages.fname'), 'class'=>'form-control')) }}
				</div>
				<!-- Last Name -->
				<div class="form-group">
					{{ Form::label('lname', Lang::get('messages.lname')) . Form::text('lname', Input::old('lname'), array('placeholder'=>Lang::get('messages.lname'), 'class'=>'form-control')) }}
				</div>
				<!-- Email -->
				<div class="form-group">
					{{ Form::label('email', 'Email') . Form::text('email', Input::old('email'), array('placeholder'=>'Email', 'class'=>'form-control')) }}
				</div>
				<!-- Password -->
				<div class="form-group">
					{{ Form::label('password', Lang::get('messages.password') ) . Form::password('password', array('placeholder'=>Lang::get('messages.password'), 'class'=>'form-control')) }}
				</div>
				<!-- Submit -->
				{{ Form::submit(Lang::get('messages.signup'), array('class'=>'btn btn-default')) }}
				{{ Form::token() . Form::close() }}
			</div>
		</div>
		<div class="row">
			<div social-login message="{{ trans('messages.signup') }}"></div>
		</div>
	</div>
@endsection