@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>Edit / Create Group Profile</h1>
				@if($group->id > 0)
				<p>If you would like to manage the members of your group, <a href="/groups/members/{{ $group->id }}">click here</a></p>
				@endif
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('url' => 'groups/edit', 'method'=>'PUT' )) }}
					
					@if($group->id > 0)
					<div class="form-group">
						<b>Group Status: {{ $group->status }}</b>
					</div>
					<input type="hidden" name="groupId" value="{{ $group->id }}"/>
					@endif
					
					<div class="form-group">
						<label for="gname">Group Name:</label>
						<input type="text" class="form-control" name="gname" id="gname" placeholder="Enter Group Name" value="{{ $group->name }}"/>
					</div>
					<div class="form-group">
						<label for="dname">Display Name:</label>
						<input type="text" class="form-control" name="dname" id="dname" placeholder="Enter the Display Name" value="{{ $group->display_name }}"/>
					</div>
					<div class="form-group">
						<label for="address1">Address 1:</label>
						<input type="text" class="form-control" name="address1" id="address1" placeholder="Enter Address Line 1" value="{{ $group->address1 }}"/>
					</div>
					<div class="form-group">
						<label for="address2">Address 2:</label>
						<input type="text" class="form-control" name="address2" id="address2" placeholder="Enter Address Line 2" value="{{ $group->address2 }}"/>
					</div>
					<div class="form-group">
						<label for="city">City:</label>
						<input type="text" class="form-control" name="city" id="city" placeholder="City" value="{{ $group->city }}"/>
					</div>
					<div class="form-group">
						<label for="state">State:</label>
						{{ Form::select('state', array('default' => 'Please Select') + Geography::getUSStates(), !empty($group->state) ? $group->state : 'default', array('class'=>'form-control')) }}
					</div>
					<div class="form-group">
						<label for="postal">Postal Code:</label>
						<input type="text" class="form-control" name="postal" id="postal" placeholder="Enter Postal / Zip Code" value="{{ $group->postal_code }}"/>
					</div>
					<div class="form-group">
						<label for="phone">Contact Phone:</label>
						<input type="text" class="form-control" name="phone" id="phone" placeholder="Enter Contact Phone Number" value="{{ $group->phone_number }}"/>
					</div>
					<button type="submit" class="btn btn-default">Submit</button>
					
					{{ Form::token() }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@endsection