<div class="row">
	<h1>Welcome to Madison</h1>
</div>
<div class="row">
	<ul ng-controller="RecentDocsController" ng-init="init()">
		<li ng-repeat="doc in docs">
			<a href="/docs/<% doc.slug %>">
				<% doc.title %>
			</a>
			<div class="list-doc-info">
				<span class="doc-created-date">Posted <% doc.created_at | date:'mediumDate' %></span>
				<span class="doc-updated-date">Updated <% doc.updated_at | date:'mediumDate' %></span>
				<span class="doc-action-count"></span>
			</div>
		</li>
	</ul>
</div>