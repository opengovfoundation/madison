<div id="participate-activity" class="participate-activity">
	<div class="activity-thread">
		<div ng-hide="annotations.length">
			{{ trans('messages.noannotations') }}
		</div>
    	<div class="row" ng-repeat="annotation in annotations | orderBy:activityOrder:true track by $id(annotation)" ng-class="annotation.label">
        	<div annotation-item activity-item-link="@{{ annotation.link }}"></div>
    	</div>
	</div>
</div>

