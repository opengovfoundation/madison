@extends('layouts/main')
@section('content')
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="/dashboard">{{ trans('messages.dashboard') }}</a></li>
			<li class="active">{{ trans('messages.notifications') }}</li>
		</ol>
	</div>
	<div class="row content">
		<h1>{{ trans('messages.notifications') }}</h1>
		<p>{{ trans('messages.selectnotif') }}</p>
		<form action="/dashboard/notifications" method="post">
			<div class="form-group">
				<?php echo Form::select('notifications[]', $validNotifications, $selectedNotifications, array('multiple' => '', 'class' => 'form-control')); ?>
				{{ Form::token() }}
				<input type="submit" class="btn btn-default" value="{{ trans('message.savesettings') }}">
			</div>
		</form>
	</div>
@endsection