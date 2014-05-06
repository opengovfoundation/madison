@extends('layouts.main')
@section('content')
	<div class="content col-md-12" ng-controller="UserPageController" ng-init="init()">
		<div class="row user-info">
			<script src="http://crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js"></script>
			<img ng-src="http://www.gravatar.com/avatar/@{{ user.email | gravatar }}" class="img-rounded img-responsive user-gravatar" alt="" />
			<h1 class="user-name">@{{ user.fname }} @{{ user.lname }}</h1>
			<span class="user-verified" ng-show="verified">Verified</span>	
			<span class="user-created-date" ng-if="user.created_at">Member since @{{ user.created_at | parseDate | date:'mediumDate'  }}</span>
		</div>
		<div class="row">
			<tabset>
				<tab heading="sponsored" ng-show="showVerified()">
					<ul class="user-sponsored-docs" ng-show="showVerified()">
						<li class="user-sponsored-doc" ng-repeat="doc in docs">
							<a href="/docs/@{{ doc.slug }}">@{{ doc.title }}</a>
							<div class="list-doc-info">
								<span class="doc-created-date">Posted @{{ doc.created_at | parseDate | date:'mediumDate' }}</span>
								<span class="doc-updated-date">Updated @{{ doc.created_at | parseDate | date:'mediumDate' }}</span>
							</div>
						</li>
					</ul>
				</tab>
				<tab heading="activity">
					<ul class="user-activity-items">
						<li class="user-activity-item" ng-repeat="activity in activities | orderBy:activityOrder:true">
							<span.user-activity-title>Added a @{{ activity.label }} to the text of <div doc-link doc-id="@{{ activity.doc_id }}"></div></span>
							<span class="user-activity-date">@{{ activity.created_at | parseDate | date:'mediumDate' }}</span>
							<blockquote>@{{ activity.text }}</blockquote>
						</li>
					</ul>
				</tab>
			</tabset>
		</div>
	</div>
@endsection