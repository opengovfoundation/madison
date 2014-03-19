<h3>Activity</h3>
<div class="activity-thread">
    <div class="activity-item" ng-repeat="activity in activities" ng-class="activity.label">
        <div class="activity-author">
            <span ng-if="activity.label == 'comment'"><% activity.user.fname %> <% activity.user.lname.substr(0,1) %></span>
            <span ng-if="activity.label == 'annotation'"><% activity.user.name %></span>
            
        </div>
        <div class="activity-content">
            <span ng-if="activity.label == 'comment'"><% activity.content %></span>
            <span ng-if="activity.label" == 'annotation'><% activity.text %></span>
        </div>
    </div>
</div>