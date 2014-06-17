<h3>Activity</h3>
<div class="activity-thread col-md-12">
    <div id="@{{ activity.label == 'comment' ? 'comment_' + activity.id : '' }}" class="row activity-item" ng-repeat="activity in activities | orderBy:activityOrder:true track by $id(activity)" ng-class="activity.label">
        <div activity-item activity-item-link="@{{ activity.link }}"></div>
    </div>
</div>
