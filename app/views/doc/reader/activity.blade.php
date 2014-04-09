<h3>Activity</h3>
<div class="activity-thread col-md-12">
    <div class="row activity-item" ng-repeat="activity in activities | orderBy:activityOrder:true track by $id(activity)" ng-class="activity.label">
        <div class="row">
            <div class="activity-author col-md-10">
                <span><% activity.user.name || (activity.user.fname + ' ' + activity.user.lname.substr(0,1)) %></span>
            </div>
            <div class="activity-icon col-md-2">
                <span class="glyphicon" ng-class="{'glyphicon-comment': activity.label=='comment', 'glyphicon-edit': activity.label=='annotation'}"></span>
            </div>
        </div>
        <div class="row">
            <div class="activity-content col-md-12">
                <span><% activity.text %></span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <span class="activity-replies-indicator" ng-click="collapseComments(activity)"><span class="glyphicon glyphicon-share-alt"></span><% activity.comments.length || '0' %> Replies</span>
            </div>
            <div class="col-md-6 activity-actions">
                <span class="glyphicon glyphicon-thumbs-up" ng-click="addAction(activity, 'likes', $event)">(<% activity.likes || '0' %>)</span>
                <span class="glyphicon glyphicon-thumbs-down" ng-click="addAction(activity, 'dislikes', $event)">(<% activity.dislikes || '0' %>)</span>
                <span class="glyphicon glyphicon-flag" ng-click="addAction(activity, 'flags', $event)">(<% activity.flags || '0' %>)</span>
            </div>
        </div>
        <div class="activity-replies row" collapse="activity.commentsCollapsed">
            <div class="activity-reply col-md-12" ng-repeat="comment in activity.comments">
                <div class="reply-author row">
                    <div class="col-md-6">
                        <span class="glyphicon glyphicon-share-alt"></span> <% comment.user.name || (comment.user.fname + ' ' + comment.user.lname.substr(0,1)) %>:
                    </div>
                </div>
                <div class="reply-text row">
                    <div class="col-md-12">
                        <% comment.text %>
                    </div>
                </div>
            </div>
            @if(Auth::check())
                <div class="subcomment-field col-md-12">
                    <form name="add-subcomment-form" ng-submit="subcommentSubmit(activity, subcomment)">
                        <input ng-model="subcomment.text" type="text" class="form-control centered" placeholder="Add a comment" required />
                    </form>
                </div>
            @endif
        </div>
        <div class="row" ng-show="user.isSponsor && ((activity.user_id != user.id && activity.label === 'comment') || (activity.user.id != user.id && activity.label === 'annotation'))">
            <div class="col-md-12">
                <span class="btn btn-default" ng-if="activity.seen === 0" ng-click="notifyAuthor(activity)">Mark as seen</span>
                <span class="glyphicon glyphicon-ok" ng-if="activity.seen === 1">Marked as seen!</span>
            </div>
        </div>  
        <div class="row" ng-hide="user.isSponsor">
            <div class="col-md-12">
                <span class="glyphicon glyphicon-ok" ng-if="activity.seen === 1"> A sponsor marked this as seen!</span>
            </div>
        </div>
    </div>
    </div>
</div>
