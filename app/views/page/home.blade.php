<div class="home">
	<div class="intro">
		<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1><i class="i participa-logo">{{ trans('messages.sitetitle') }}</i></h1>
						<p class="lead">{{ trans('messages.tagline') }}</p>
					</div>
				</div>
		</div>
	</div>
	
	<div class="home-docs container">
		<div ng-controller="HomePageController" tourtip="@{{ step_messages.step_0 }}" tourtip-step="0">

			<div class="home-docs-filters row">
				<div class="col-sm-6">
					<input tourtip="@{{ step_messages.step_1 }}" tourtip-step="1" id="doc-text-filter" type="text" ng-model="docSearch" class="form-control" placeholder="{{ trans('messages.filter') }}">
				</div>
				<div class="col-sm-4 home-select2-container">
					<select id="doc-category-filter" ui-select2="select2Config" ng-model="select2">
						<option value=""></option>
						<optgroup label="{{ trans('messages.category') }}">
							<option value="category_@{{ category.id }}" ng-repeat="category in categories">@{{ category.name }}</option>
						</optgroup>
						<optgroup label="{{ trans('messages.sponsor') }}">
							<option value="sponsor_@{{ sponsor.id }}" ng-repeat="sponsor in sponsors">@{{ sponsor.fname }} @{{ sponsor.lname }}</option>
						</optgroup>
						<optgroup label="{{ trans('messages.status') }}">
							<option value="status_@{{ status.id}}" ng-repeat="status in statuses">@{{ status.label}}</option>
						</optgroup>
					</select>
				</div>
				<div class="col-sm-2 home-select2-container">
					<select id="doc-date-filter" ui-select2="dateSortConfig" id="dateSortSelect" ng-model="dateSort">
						<option value=""></option>
						<option value="created_at">{{ trans('messages.posted') }}</option>
						<option value="updated_at">{{ trans('messages.updated') }}</option>
					</select>
				</div>
			</div>

			<div class="home-docs-list row">
				<div class="col-sm-12">
					<ul class="docs-list list-unstyled">
						<li ng-repeat="doc in docs | toArray | filter:docSearch | orderBy:dateSort:reverse" ng-show="docFilter(doc)">
							<div doc-list-item></div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>