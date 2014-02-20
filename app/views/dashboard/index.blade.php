@extends('layouts/main')
@section('content')
	<div class="row">
		<ol class="breadcrumb">
			<li class="active">Dashboard</li>
		</ol>
	</div>
	<div class="row content">
		<ul>
			<li>{{ HTML::link('dashboard/settings', 'Settings') }}</li>
			<li>{{ HTML::link('dashboard/docs', 'Create / Edit Documents') }}</li>
			<li>{{ HTML::link('dashboard/verifications', 'Verify Accounts') }}</li>
		</ul>
	</div>
@endsection