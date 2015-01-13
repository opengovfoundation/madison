@extends('layouts/main')
@section('content')
<div class="container">
	<div class="row">
		<div class="col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3">
			<div class="content">
				<h1>Forgot Password</h1>
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
</div>
@endsection
