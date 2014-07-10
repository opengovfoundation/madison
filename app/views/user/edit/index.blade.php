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
				@if($errors->has())
  					@foreach ($errors->all() as $error)
      					<div class="alert alert-danger">{{ $error }}</div>
  					@endforeach
				@endif
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
					<!-- Phone -->
					<div class="form-group">
						<label for="phone">Phone number:</label>
						<input type="tel" class="form-control" name="phone" id="phone" placeholder="Enter phone" value="{{ Auth::user()->phone }}"/>
					</div>
					<!-- TODO: Organization -->
					<!-- Location -->
					<!-- TODO: autofill / check location exists -->
					<div class="checkbox">
						@if(Auth::user()->verified())
							<label>
								<input name="verify" type="checkbox" checked disabled> Request 'Verified Account' is '{{ Auth::user()->verified() }}'
							</label>
						@else
							<label>
								<input name="verify" type="checkbox"> Request 'Verified Account'
							</label>
						@endif
					</div>
					<div class="form-group">
						@if(Auth::user()->hasRole('Independent Sponsor'))
							<p><span class="glyphicon glyphicon-check"></span> Your account is able to sponsor documents as an individual.</p>
						@elseif(Auth::user()->getSponsorStatus() && Auth::user()->getSponsorStatus()->meta_value == 0)
							<p>Your request to become an Independent Sponsor is 'pending'</p>
						@else
							<p>Want to be a document sponsor? <a href="/documents/sponsor/request">Request to be an Independent Sponsor</a></p>
						@endif
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