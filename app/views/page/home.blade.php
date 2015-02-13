<div class="main-banner">
  <div class="container">
    <h1><strong>participa<span class="red">.</span>gob<span class="red">.</span>mx</strong></h1>
    <p class="text-sub">Una plataforma que permite a la ciudadanía participar <br>de manera dinámica, abierta y orientada a la discusión.</p>
    <p  class="text-sub-2"><strong>participa.gob.mx</strong> cuenta con tres herramientas <br>de participación: <strong>Encuesta</strong> + <strong>Foro</strong> + <strong>Co-Edición</strong></p>
  </div>
</div>

<div class="home-docs container">
	<div class="row">
		<div class="col-lg-12 col-md-12">
			<h4>Temas y/o encuestas de participación</h4>
			<hr class="red">
			<div ng-controller="HomePageController" tourtip="@{{ step_messages.step_0 }}" tourtip-step="0" tourtip-next-label="Siguiente">
				<div class="home-docs-filters row">
					<div class="col-sm-6">
						<input tourtip="@{{ step_messages.step_1 }}" tourtip-step="1" tourtip-next-label="Siguiente" id="doc-text-filter" type="text" ng-model="docSearch" class="form-control" placeholder="{{ trans('messages.filter') }}">
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
				<div class="docs-list list-unstyled">
					<div ng-repeat="doc in docs | toArray | filter:docSearch | orderBy:dateSort:reverse" ng-show="docFilter(doc)">
						<div doc-list-item></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
