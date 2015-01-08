@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		<div class="row">
			<div class="md-col-12">
				<h1>{{ trans('messages.editprofile') }}</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				{{ Form::open(array('url'=>'user/edit/' . $user->id, 'method'=>'PUT' )) }}
					<!-- First Name -->
					<div class="form-group">
						<label for="fname">{{ trans('messages.fname') }}:</label>
						<input type="text" class="form-control" name="fname" id="fname" placeholder="{{ trans('messages.enterfname') }}" value="{{ $user->fname }}"/>
					</div>
					<!-- Last Name -->
					<div class="form-group">
						<label for="fname">{{ trans('messages.lname') }}:</label>
						<input type="text" class="form-control" name="lname" id="lname" placeholder="{{ trans('messages.enterlname') }}" value="{{ $user->lname }}"/>
					</div>
					<!-- Email -->
					<div class="form-group">
						<label for="email">{{ trans('messages.emailaddress') }}:</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="{{ trans('messages.enteremail') }}" value="{{ $user->email}}"/>
					</div>
					<!-- URL -->
					<div class="form-group">
						<label for="url">URL:</label>
						<input type="url" class="form-control" name="url" id="url" placeholder="{{ trans('messages.enterurl') }}" value="{{ $user->url }}"/>
					</div>
					<!-- Phone -->
					<div class="form-group">
						<label for="phone">{{ trans('messages.phone') }}:</label>
						<input type="tel" class="form-control" name="phone" id="phone" placeholder="{{ trans('messages.enterphone') }}" value="{{ $user->phone }}"/>
					</div>
					<!-- TODO: Organization -->
					<!-- Location -->
					<!-- TODO: autofill / check location exists -->
					<!-- Password -->
					<div class="form-group">
						<label for="password_1">{{ trans('messages.chgpass') }}:</label>
						<input type="password" class="form-control" name="password_1" id="password_1" placeholder="{{ trans('messages.newpass') }}" value=""/>
					</div>
					<div class="form-group">
						<label for="password_2">{{ trans('messages.cnfpass') }}:</label>
						<input type="password" class="form-control" name="password_2" id="password_2" placeholder="{{ trans('messages.repeatpass') }}" value=""/>
					</div>
					<div class="checkbox">
						@if($user->verified())
							<label>
								<input name="verify" id="verify" type="checkbox" checked disabled> {{ trans('messages.reqveraccount') }} is '{{ $user->verified() }}'
							</label>
						@else
							<label>
								<input name="verify" id="verify" type="checkbox"> {{ trans('messages.reqveraccount') }}'
							</label>
						@endif
					</div>
					<div class="form-group">
						@if($user->hasRole('Independent Sponsor'))
							<p><span class="glyphicon glyphicon-check"></span> Your account is able to sponsor documents as an individual.</p>
						@elseif($user->getSponsorStatus() && $user->getSponsorStatus()->meta_value == 0)
							<p>Your request to become an Independent Sponsor is 'pending'</p>
						@else
							<p>{{ trans('messages.besponsor') }} <a href="/documents/sponsor/request">{{ trans('messages.reqindepsponsor') }}</a></p>
						@endif
					</div>
					<div class="form-group">
						<!-- Change avatar at gravatar.com -->
						<a href="https://gravatar.com" target="_blank" class="red">{{ trans('messages.chggravatar') }} Gravatar.com</a>
					</div>
					<button type="submit" class="btn btn-default" id="submit">{{ trans('messages.submit') }}</button>
					{{ Form::token() }}
				{{ Form::close() }}
			</div>
		</div>
	</div>
@endsection