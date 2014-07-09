@extends('layouts/main')
@section('content')
	<div class="row">
		<ol class="breadcrumb">
			<li><a href="/dashboard">Dashboard</a></li>
			<li class="active">Notifications</li>
		</ol>
	</div>
	<div class="row content">
	<p>Please select the notifications you would like to recieve via e-mail.</p>
	<form action="/dashboard/notifications" method="post">
		<?php echo Form::select('notifications[]', $validNotifications, $selectedNotifications, array('multiple')); ?>
		{{ Form::token() }}
		<input type="submit" value="Save Settings">
		
	</form>
	
	</div>
@endsection