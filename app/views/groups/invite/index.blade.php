@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>Add Member to '{{ $group->name }}'</h1>
				<p>Please type in the e-mail address of the user you would like to add to this group and select their role.</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('method' => 'PUT')) }}
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="text" class="form-control" name="email" id="email" placeholder="Enter User's e-mail"/>
					</div>
					<div class="form-group">
						<label for="role">User's Role:</label>
						{{ Form::select('role', Group::getRoles(true), Group::ROLE_STAFF, array('class' => 'form-control', 'id' => 'role')) }}
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
					{{ Form::token() }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@endsection
