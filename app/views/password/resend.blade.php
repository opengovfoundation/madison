@extends('layouts/main')
@section('content')
<div class="content col-md-12">
	<div class="row">
		<div class="md-col-12">
			<h1>Resend confirmation email</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<form class="reset-form" action="{{ action('RemindersController@postConfirmation') }}" method="POST">
				<p>Please fill out the form below to resend your confirmation email.</p>
				<div class="form-group">
					<label for="email">Email Address</label>
			    	<input id="email" type="email" name="email" class="form-control">
			    </div>
			    <div class="form-group">
			    	<label for="password">Password</label>
			    	<input id="password" type="password" name="password" class="form-control">
			    </div>
			    <input class="btn btn-default" type="submit" value="Resend my confirmation email">
			</form>
		</div>
	</div>
</div>

@endsection
