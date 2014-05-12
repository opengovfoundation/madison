@extends('layouts/main')
@section('content')
	<div class="row">
		<div class="col-md-3">
			<ol class="breadcrumb">
				<li><a href="/dashboard">Dashboard</a></li>
				<li class="active">Settings</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="content col-md-12">
			<h1>Settings</h1>
			<table class="table table-striped" ng-controller="DashboardSettingsController" ng-init="init()">
				<thead>
					<th>Contact</th>
					<th>First Name</th>
					<th>Last Name</th>
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
@endsection