@extends('layouts/main')
@section('content')
	<div class="row">
		<div class="col-md-3">
			<ol class="breadcrumb">
				<li><a href="/dashboard">Dashboard</a></li>
				<li class="active">Verify Account</li>
			</ol>
		</div>
	</div>
	<div class="row">
		<div class="content col-md-12" ng-controller="DashboardVerifyController" ng-init="init()">
			<h1>Verify Users</h1>
			<ul>
				@foreach($requests as $request)
				<li ng-repeat="request in requests">
					<span><% request.user.fname %> <% request.user.lname %> : </span>
					<span><% request.meta_value %></span>
					<span>
						<div class="btn-group">
							<button type="button" class="btn btn-success" ng-class="{active: request.meta_value == 'verified'}" ng-click="update(request, 'verified', event)">Verify</button>
							<button type="button" class="btn btn-danger" ng-class="{active: request.meta_value == 'denied'}" ng-click="update(request, 'denied', event)">Deny</button>
						</div>
					</span>
				</li>
				@endforeach
			</ul>
		</div>
	</div>
	
@endsection