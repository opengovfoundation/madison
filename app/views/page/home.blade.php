<div class="row">
	<h1>Welcome to Madison</h1>
</div>
<div class="row" ng-controller="HomePageController" ng-init="init()">
	<div class="col-sm-6">
		<input type="text" ng-model="docSearch" class="form-control" placeholder="Search document titles">
	</div>
	<div class="col-sm-4 col-sm-offset-2">
		<div class="row">
			<select ui-select2="select2Config" ng-model="select2">
				<option value=""></option>
				<optgroup label="Category">
					<option value="@{{ category }}" ng-repeat="category in categories">@{{ category }}</option>
				</optgroup>
				<optgroup label="Sponsor">
					<option value="@{{ sponsor.id }}" ng-repeat="sponsor in sponsors">@{{ sponsor.fname }} @{{ sponsor.lname }}</option>
				</optgroup>
				<optgroup label="Status">
					<option value="@{{ status.id}}" ng-repeat="status in statuses">@{{ status.label}}</option>
				</optgroup>
			</select>
		</div>
		<div class="row">
			<select ui-select2="dateSortConfig" id="dateSortSelect" ng-model="dateSort">
				<option value=""></option>
				<option value="created_at">Date Posted</option>
				<option value="updated_at">Last Updated</option>
			</select>
		</div>
	</div>
	<div class="col-sm-12">
		<ul>
			<li ng-repeat="doc in docs | toArray | filter:docSearch | orderBy:dateSort:reverse" ng-show="docFilter(doc)">
				<a href="/docs/@{{ doc.slug }}">
					@{{ doc.title }}
				</a>
				<span class="doc-categories">
					<span class="category" ng-repeat="category in doc.categories">@{{ category.name }}</span>
				</span>
				<span class="doc-statuses">
					<span class="status" ng-repeat="status in doc.statuses">@{{ status.label }}</span>
				</span>
				<div class="list-doc-info">
					<span class="doc-created-date">Posted @{{ doc.created_at | date:'mediumDate' }}</span>
					<span class="doc-updated-date">Updated @{{ doc.updated_at | date:'mediumDate' }}</span>
					<span class="doc-dates">
						<span class="date" ng-repeat="date in doc.dates">@{{ date.label }} on @{{ date.date | date:'mediumDate' }}</span>
					</span>

				</div>
			</li>
		</ul>
	</div>
</div>
