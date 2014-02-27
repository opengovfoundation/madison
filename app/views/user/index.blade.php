@extends('layouts.main')
@section('content')
	<div class="content col-md-12" ng-controller="UserPageController" ng-init="init()">
		<div class="row user-info">
			<img ng-src="http://www.gravatar.com/avatar/<% user.email | gravatar %>" class="img-rounded img-responsive user-gravatar" alt="" />
			<h1 class="user-name"><% user.fname %> <% user.lname %></h1>
			<span class="user-verified" ng-show="verified">Verified</span>	
			<span class="user-created-date">Member since <% user.created_at | date:'mediumDate'  %></span>
		</div>
		<div class="row">
			<tabset>
				<tab heading="sponsored" ng-show="showVerified()">
					<ul class="user-sponsored-docs">
						<li class="user-sponsored-doc" ng-repeat="doc in docs">
							<a href="/docs/<% doc.slug %>"><% doc.title %></a>
							<div class="list-doc-info">
								<span class="doc-created-date">Posted <% doc.created_at | date:'mediumDate' %></span>
								<span class="doc-updated-date">Updated <% doc.updated_at | date:'mediumDate' %></span>
							</div>
						</li>
					</ul>
				</tab>
				<tab heading="activity">
					<ul class="user-activity-items">
						<li class="user-activity-item" ng-repeat="comment in comments">
							<span.user-activity-title>Added a comment to the text of <div doc-link doc-id="<% comment.doc_id %>"></div></span>
							<span class="user-activity-date"><% comment.created_at | date:'mediumDate' %></span>
							<blockquote><% comment.content %></blockquote>
						</li>
					</ul>
				</tab>
			</tabset>
		</div>
	</div>
@endsection