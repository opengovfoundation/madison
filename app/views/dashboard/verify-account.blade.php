@extends('layouts/main')
@section('content')
	<div class="container">
		<div class="row">
			<ol class="breadcrumb">
				<li><a href="/dashboard">Dashboard</a></li>
				<li class="active">Verify Account</li>
			</ol>
			<div class="col-md-12" ng-controller="DashboardVerifyController" ng-init="init()">
				<div class="content">
					<h1>Verify Users</h1>
					<table class="table table-striped">
						<thead>
							<th>ID</th>
							<th>First Name</th>
							<th>Last Name</th>
							<th>Email</th>
							<th>Phone</th>
							<th>Status</th>
						</thead>
						<tbody>
							<tr ng-repeat="request in requests">
								<td>@{{ request.id }}</td>
								<td>@{{ request.user.fname}}</td>
								<td>@{{ request.user.lname }}</td>
								<td>@{{ request.user.email }}</td>
								<td>@{{ request.user.phone }}</td>
								<td>
									<div class="btn-group">
										<button type="button" class="btn btn-success" ng-class="{active: request.meta_value == 'verified'}" ng-click="update(request, 'verified')">Verified</button>
										<button type="button" class="btn btn-warning" ng-class="{active: request.meta_value == 'pending'}" ng-click="update(request, 'pending')">Pending</button>
										<button type="button" class="btn btn-danger" ng-class="{active: request.meta_value == 'denied'}" ng-click="update(request, 'denied')">Denied</button>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	
@endsection