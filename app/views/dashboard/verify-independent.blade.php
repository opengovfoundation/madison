@extends('layouts/main')
@section('content')
	<div class="row">
		<div class="col-md-3">
			<ol class="breadcrumb">
				<li><a href="/dashboard">Dashboard</a></li>
				<li class="active">Verify Independent Sponsors</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="content col-md-12" ng-controller="DashboardVerifyUserController" ng-init="init()">
			<h1>Verify Independent Sponsors</h1>
			<table class="table table-striped">
				<thead>
					<th>ID</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Status</th>
				</thead>
				<tbody>
					<tr ng-repeat="request in requests">
						<td>@{{ request.user.id }}</td>
						<td>@{{ request.user.fname }}</td>
						<td>@{{ request.user.lname }}</td>
						<td>
							<div class="btn-group">
								<button type="button" class="btn btn-success" ng-click="update(request, 'verified')">Verified</button>
								<button type="button" class="btn btn-danger"  ng-click="update(request, 'denied')">Denied</button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
@endsection