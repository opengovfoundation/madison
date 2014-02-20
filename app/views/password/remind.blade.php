@extends('layouts/main')
@section('content')
<div class="content col-md-12">
	<div class="row">
		<div class="md-col-12">
			<h1>Forgot Password</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<form class="reset-form" action="{{ action('RemindersController@postRemind') }}" method="POST">
				<p>Please fill out your email address below, and a reset password
					email will be sent to you.</p>
				<div class="form-group">
			    	<label for="email">Email Address</label>
			    	<input id="email" type="email" name="email" placeholder="email@example.com" class="form-control" />
			    </div>
			    <div class="form-group">
			    	<input class="btn btn-default" type="submit" value="Send Password Reset">
			    </div>
			    {{ Form::token() }}
			</form>
		</div>
	</div>
</div>


@endsection
