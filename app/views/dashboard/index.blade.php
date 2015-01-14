@extends('layouts/main')
@section('content')
	<div class="container">
		<div class="row">
			<ol class="breadcrumb">
				<li class="active">{{ trans('messages.dashboard') }}</li>
			</ol>
			<div class="col-md-12">
				<div class="content">
					<ul class="list-unstyled">
						<li>{{ HTML::link('dashboard/settings', Lang::get('messages.settings')) }}</li>
						<li>{{ HTML::link('dashboard/docs', Lang::get('messages.createeditdocs')) }}</li>
						<li>{{ HTML::link('dashboard/verifications', Lang::get('messages.verifyaccounts')) }}</li>
						<li>{{ HTML::link('dashboard/groupverifications', Lang::get('messages.verifygroups')) }}</li>
						<li>{{ HTML::link('dashboard/userverifications', Lang::get('messages.verifyindiesponsors')) }}</li>
						<li>{{ HTML::link('dashboard/notifications', Lang::get('messages.notifications')) }}</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@endsection