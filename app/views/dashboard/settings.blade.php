@extends('layouts/main')
@section('content')
	<div class="container">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="/dashboard">{{ trans('messages.dashboard') }}</a></li>
				<li class="active">{{ trans('messages.settings') }}</li>
			</ol>
			<div class="col-md-12">
				<div class="content">
					<h1>Settings</h1>
					<table class="table table-striped" ng-controller="DashboardSettingsController" ng-init="init()">
						<thead>
							<th>Contact</th>
							<th>{{ trans('messages.fname') }}</th>
							<th>{{ trans('messages.lname') }}</th>
							<th>Email</th>
						</thead>
						<tbody>
							<tr ng-repeat="admin in admins">
								<td><input type="checkbox" ng-model="admin.admin_contact" ng-change="saveAdmin(admin)"><span class="glyphicon glyphicon-refresh" ng-show="admin.saved == false"></span></td>
								<td>@{{ admin.fname }}</td>
								<td>@{{ admin.lname }}</td>
								<td>@{{ admin.email }}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
@endsection