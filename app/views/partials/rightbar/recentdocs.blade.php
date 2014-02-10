<div class="rightbar recentdocs col-md-3 col-md-offset-1" ng-controller="RecentDocsController" ng-init="init()">
	<h3>Featured Documents</h3>	
	<ul>
		<li ng-repeat="doc in docs">
			<a href="/docs/<% doc.slug %>">
				<h4><% doc.title %></h4>	
			</a>
		</li>
	</ul>
</div>