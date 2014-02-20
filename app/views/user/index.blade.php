@extends('layouts.main')
@section('content')
	<div class="content col-md-12" ng-controller="UserPageController" ng-init="init()">
		<div class="row">
			<div class="col-md-2">
				<img ng-src="http://www.gravatar.com/avatar/<% user.email | gravatar %>" class="img-rounded img-responsive" alt="" />
			</div>
			<div class="col-md-10">
				<div class="row">
					<h1 class="user-name"><% user.fname %> <% user.lname %></h1>
					<span class="user-verified" ng-show="verified">Verified</span>	
				</div>
				<div class="row">
					<span class="user-created-date">Member since <% user.created_at | date:'mediumDate'  %></span>
				</div>
			</div>
		</div>
		<div class="row">
			
		</div>
	</div>
@endsection