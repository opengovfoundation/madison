@extends('layouts/main')
@section('content')
	<div class="row">
		<ol class="breadcrumb">
			<li class="active">{{ trans('messages.dashboard') }}</li>
		</ol>
	</div>
	<div class="row content">
		<ul>
			<li>{{ HTML::link('dashboard/settings', Lang::get('messages.settings')) }}</li>
			<li>{{ HTML::link('dashboard/docs', Lang::get('messages.createeditdocs')) }}</li>
			<li>{{ HTML::link('dashboard/verifications', Lang::get('messages.verifyaccounts')) }}</li>
			<li>{{ HTML::link('dashboard/groupverifications', Lang::get('messages.verifygroups')) }}</li>
			<li>{{ HTML::link('dashboard/userverifications', Lang::get('messages.verifyindiesponsors')) }}</li>
			<li>{{ HTML::link('dashboard/notifications', Lang::get('messages.notifications')) }}</li>
		</ul>
	</div>
@endsection