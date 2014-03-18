<h3>Activity</h3>
<div class="activity-thread">
    <div class="activity-item" ng-repeat="activity in activities" ng-class="activity.label">
        <span ng-if="activity.label == 'comment'"><% activity.content %></span>
        <span ng-if="activity.label" == 'annotation'><% activity.text %></span>
    </div>
</div>