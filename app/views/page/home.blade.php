<div class="row">
	<h1>Welcome to Madison</h1>
</div>
<div class="row" ng-controller="HomePageController" ng-init="init()">
	<select ui-select2="select2Config" ng-model="select2" placeholder="Filter documents by category">
		<option value=""></option>
		<optgroup label="Category">
			<option value="<% category %>" ng-repeat="category in categories"><% category %></option>
		</optgroup>
	</select>
	<ul>
		<li ng-repeat="doc in docs" >
			<a href="/docs/<% doc.slug %>">
				<% doc.title %>
			</a>
			<div class="list-doc-info">
				<span class="doc-created-date">Posted <% doc.created_at | date:'mediumDate' %></span>
				<span class="doc-updated-date">Updated <% doc.updated_at | date:'mediumDate' %></span>
				<span class="doc-action-count"><% doc.annotationCount %></span>
			</div>
		</li>
	</ul>
</div>