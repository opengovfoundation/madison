<div class="row">
	<h1>Welcome to Madison</h1>
</div>
<div class="row" ng-controller="HomePageController" ng-init="init()">
	<select ui-select2="select2Config" ng-model="select2">
		<option value=""></option>
		<optgroup label="Category">
			<option value="<% category %>" ng-repeat="category in categories | unique"><% category %></option>
		</optgroup>
		<optgroup label="Sponsor">
			<option value="<% sponsor.id %>" ng-repeat="sponsor in sponsors | unique"><% sponsor.fname %> <% sponsor.lname %></option>
		</optgroup>
		<optgroup label="Status">
			<option value="<% status.id%>" ng-repeat="status in statuses | unique"><% status.label%></option>
		</optgroup>
	</select>
	<select ui-select2="dateSortConfig" id="dateSortSelect" ng-model="dateSort">
		<option value=""></option>
		<option value="created_at">Date Posted</option>
		<option value="updated_at">Last Updated</option>
	</select>
	<ul>
		<li ng-repeat="doc in docs | orderBy:dateSort:reverse" ng-show="docFilter(doc)">
			<a href="/docs/<% doc.slug %>">
				<% doc.title %>
			</a>
			<span class="doc-categories">
				<span class="category" ng-repeat="category in doc.categories"><% category.name %></span>
			</span>
			<span class="doc-statuses">
				<span class="status" ng-repeat="status in doc.statuses"><% status.label %></span>
			</span>
			<div class="list-doc-info">
				<span class="doc-created-date">Posted <% doc.created_at | date:'mediumDate' %></span>
				<span class="doc-updated-date">Updated <% doc.updated_at | date:'mediumDate' %></span>
				<span class="doc-dates">
					<span class="date" ng-repeat="date in doc.dates"><% date.label %> on <% date.date | date:'mediumDate' %></span>
				</span>
				
			</div>
		</li>
	</ul>
</div>