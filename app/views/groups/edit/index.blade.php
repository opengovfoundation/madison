@extends('layouts.main')
@section('content')
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<h2>{{ trans('messages.editcreategroup')}}</h2>
				@if($group->id > 0)
				<p>{{ trans('messages.managemembers') }}<a href="/groups/members/{{ $group->id }}">{{ trans('messages.clickhere') }}</a></p>
				@endif

				{{ Form::open(array('url' => 'groups/edit', 'method'=>'PUT' )) }}
					
					@if($group->id > 0)
					<div class="form-group">
						<b>{{ trans('messages.groupstatus') }}: {{ $group->status }}</b>
					</div>
					<input type="hidden" name="groupId" value="{{ $group->id }}"/>
					@endif
					
					<div class="form-group">
						<label for="gname">{{ trans('messages.groupname') }}:</label>
						<input type="text" class="form-control" name="gname" id="gname" placeholder="{{ trans('messages.entergroupname') }}" value="{{ $group->name }}"/>
					</div>
					<div class="form-group">
						<label for="dname">{{ trans('messages.displayname') }}:</label>
						<input type="text" class="form-control" name="dname" id="dname" placeholder="{{ trans('messages.enterdisplayname') }}" value="{{ $group->display_name }}"/>
					</div>
					<div class="form-group">
						<label for="address1">{{ trans('messages.address1') }}:</label>
						<input type="text" class="form-control" name="address1" id="address1" placeholder="{{ trans('messages.enteraddress1') }}" value="{{ $group->address1 }}"/>
					</div>
					<div class="form-group">
						<label for="address2">{{ trans('messages.address2') }}:</label>
						<input type="text" class="form-control" name="address2" id="address2" placeholder="{{ trans('messages.enteraddress2') }}" value="{{ $group->address2 }}"/>
					</div>
					<div class="form-group">
						<label for="city">{{ trans('messages.city') }}:</label>
						<input type="text" class="form-control" name="city" id="city" placeholder="{{ trans('messages.city') }}" value="{{ $group->city }}"/>
					</div>
					<div class="form-group">
						<label for="state">{{ trans('messages.state') }}:</label>
						{{ Form::select('state', array('default' => Lang::get('messages.pleaseselect')) + Geography::getUSStates(), !empty($group->state) ? $group->state : 'default', array('class'=>'form-control')) }}
					</div>
					<div class="form-group">
						<label for="postal">{{ trans('messages.postalcode') }}:</label>
						<input type="text" class="form-control" name="postal" id="postal" placeholder="{{ trans('messages.enterpostalcode') }}" value="{{ $group->postal_code }}"/>
					</div>
					<div class="form-group">
						<label for="phone">{{ trans('messages.contactphone') }}:</label>
						<input type="text" class="form-control" name="phone" id="phone" placeholder="{{ trans('messages.entercontactphone') }}" value="{{ $group->phone_number }}"/>
					</div>
					<button type="submit" class="btn btn-default">{{ trans('messages.submit') }}</button>
					
					{{ Form::token() }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@endsection