<div class="row">
	<h1>Welcome to Madison</h1>
</div>
<div class="row" ng-controller="HomePageController" tourtip="@{{ step_messages.step_0 }}" tourtip-step="0">
	<div class="col-sm-6">
		<input tourtip="@{{ step_messages.step_1 }}" tourtip-step="1" id="doc-text-filter" type="text" ng-model="docSearch" class="form-control" placeholder="Filter document titles">
	</div>
	<div class="col-sm-4 home-select2-container">
		<select id="doc-category-filter" ui-select2="select2Config" ng-model="select2">
			<option value=""></option>
			<optgroup label="Category">
				<option value="category_@{{ category.id }}" ng-repeat="category in categories">@{{ category.name }}</option>
			</optgroup>
			<optgroup label="Sponsor">
				<option value="sponsor_@{{ sponsor.id }}" ng-repeat="sponsor in sponsors">@{{ sponsor.fname }} @{{ sponsor.lname }}</option>
			</optgroup>
			<optgroup label="Status">
				<option value="status_@{{ status.id}}" ng-repeat="status in statuses">@{{ status.label}}</option>
			</optgroup>
		</select>
	</div>
	<div class="col-sm-2 home-select2-container">
		<select id="doc-date-filter" ui-select2="dateSortConfig" id="dateSortSelect" ng-model="dateSort">
			<option value=""></option>
			<option value="created_at">Date Posted</option>
			<option value="updated_at">Last Updated</option>
		</select>
	</div>
	<div class="col-sm-12">
		<ul>
			<li ng-repeat="doc in docs | toArray | filter:docSearch | orderBy:dateSort:reverse" ng-show="docFilter(doc)">
				<div doc-list-item></div>
			</li>
		</ul>
	</div>
</div>