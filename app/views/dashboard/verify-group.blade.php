@extends('layouts/main')
@section('content')
	<div class="row">
		<div class="col-md-3">
			<ol class="breadcrumb">
				<li><a href="/dashboard">Dashboard</a></li>
				<li class="active">Verify Groups</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="content col-md-12" ng-controller="DashboardVerifyGroupController" ng-init="init()">
			<h1>Verify Groups</h1>
			<table class="table table-striped">
				<thead>
					<th>ID</th>
					<th>Group Name</th>
					<th>Display Name</th>
					<th>Status</th>
				</thead>
				<tbody>
					<tr ng-repeat="request in requests">
						<td>@{{ request.id }}</td>
						<td>@{{ request.name }}</td>
						<td>@{{ request.display_name }}</td>
						<td>
							<div class="btn-group">
								<button type="button" class="btn btn-success" ng-class="{active: request.status == '{{ Group::STATUS_ACTIVE }}'}" ng-click="update(request, '{{ Group::STATUS_ACTIVE }}')">Active</button>
								<button type="button" class="btn btn-warning" ng-class="{active: request.status == '{{ Group::STATUS_PENDING }}'}" ng-click="update(request, '{{ Group::STATUS_PENDING}}')">Pending</button>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
@endsection