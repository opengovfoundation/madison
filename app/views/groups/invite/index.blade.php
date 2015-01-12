@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>{{ trans('messages.addmemberto') }} '{{ $group->name }}'</h1>
				<p>{{ trans('messages.useremail') }}</p>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('method' => 'PUT')) }}
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="text" class="form-control" name="email" id="email" placeholder="{{ trans('messages.enterusersemail') }}"/>
					</div>
					<div class="form-group">
						<label for="role">{{ translate('messages.usersrole') }}:</label>
						{{ Form::select('role', Group::getRoles(true), Group::ROLE_STAFF, array('class' => 'form-control', 'id' => 'role')) }}
					</div>
					<button type="submit" class="btn btn-default">{{ trans('messages.submit') }}</button>
					{{ Form::token() }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@endsection