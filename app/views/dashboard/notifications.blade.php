@extends('layouts/main')
@section('content')
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="/dashboard">Dashboard</a></li>
			<li class="active">Notifications</li>
		</ol>
	</div>
	<div class="row content">
		<h1>Notifications</h1>
		<p>Please select the notifications you would like to recieve via e-mail.</p>
		<form action="/dashboard/notifications" method="post">
			<div class="form-group">
				<?php echo Form::select('notifications[]', $validNotifications, $selectedNotifications, array('multiple' => '', 'class' => 'form-control')); ?>
				{{ Form::token() }}
				<input type="submit" class="btn btn-default" value="Save Settings">
			</div>
		</form>
	</div>
@endsection