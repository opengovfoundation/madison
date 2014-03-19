<h3>Activity</h3>
<div class="activity-thread col-md-12">
    <div class="row activity-item" ng-repeat="activity in activities" ng-class="activity.label">
        <div class="row">
            <div class="activity-author col-md-10">
                <span ng-if="activity.label == 'comment'"><% activity.user.fname %> <% activity.user.lname.substr(0,1) %></span>
                <span ng-if="activity.label == 'annotation'"><% activity.user.name %></span>
            </div>
            <div class="activity-icon col-md-2">
                <span class="glyphicon" ng-class="{'glyphicon-comment': activity.label=='comment', 'glyphicon-edit': activity.label=='annotation'}"></span>
            </div>
        </div>
        <div class="row">
            <div class="activity-content col-md-12">
                <span ng-if="activity.label == 'comment'"><% activity.content %></span>
                <span ng-if="activity.label" == 'annotation'><% activity.text %></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <span class="activity-replies-indicator" ng-if="activity.comments.length > 0"><span class="glyphicon glyphicon-share-alt"></span><% activity.comments.length %> Replies</span>
            </div>
            <div class="col-md-6 activity-actions">
                <span class="glyphicon glyphicon-thumbs-up" ng-click="addAction(activity, 'likes')">(<% activity.likes || '0' %>)</span>
                <span class="glyphicon glyphicon-thumbs-down" ng-click="addAction(activity, 'dislikes')">(<% activity.dislikes || '0' %>)</span>
                <span class="glyphicon glyphicon-flag" ng-click="addAction(activity, 'flags')">(<% activity.flags || '0' %>)</span>
            </div>
        </div>
    </div>
</div>