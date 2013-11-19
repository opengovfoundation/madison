@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>Edit Profile</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('url'=>'user/edit/' . Auth::user()->id, 'method'=>'PUT' )) }}
					<!-- First Name -->
					<div class="form-group">
						<label for="fname">First Name:</label>
						<input type="text" class="form-control" name="fname" id="fname" placeholder="Enter First Name" value="{{ Auth::user()->fname }}"/>
					</div>
					<!-- Last Name -->
					<div class="form-group">
						<label for="fname">Last Name:</label>
						<input type="text" class="form-control" name="lname" id="lname" placeholder="Enter Last Name" value="{{ Auth::user()->lname }}"/>
					</div>
					<!-- Email -->
					<div class="form-group">
						<label for="email">Email Address:</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Enter email" value="{{ Auth::user()->email}}"/>
					</div>
					<!-- URL -->
					<div class="form-group">
						<label for="url">URL:</label>
						<input type="url" class="form-control" name="url" id="url" placeholder="Enter URL" value="{{ Auth::user()->url }}"/>
					</div>
					<!-- TODO: Organization -->
					<!-- Location -->
					<!-- TODO: autofill / check location exists -->
					<div class="form-group">
						<label for="location">Location:</label>
						<input type="text" class="form-control" name="location" id="location" placeholder="Enter Location" value="{{ Auth::user()->location }}"/>
					</div>
					<div class="form-group">
						<!-- Change avatar at gravatar.com -->
						<a href="https://gravatar.com" target="_blank" class="red">Change your avatar at Gravatar.com</a>
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
					{{ Form::token() }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@endsection