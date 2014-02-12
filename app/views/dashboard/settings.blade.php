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
		<div class="content col-md-12" ng-controller="DashboardSettingsController" ng-init="init()">
			<h1>Settings</h1>
			<form role="form" name="settingsForm" action="/dashboard/settings" method="POST">
				<div class="form-group" >
					<label class="control-label" for="adminContact">Admin Contact Email</label>
					<input ng-model="contact" typeahead="email for email in emails | filter:$viewValue" typeahead-editable="false" type="email" class="form-control" placeholder="Admin Contact Email" value="<% selected %>" required/>
				</div>
				<button type="submit" class="btn btn-default">Submit</button>
			</form>
		</div>
	</div>
@endsection