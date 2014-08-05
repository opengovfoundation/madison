@if(Auth::check())
<div id="participate-comment" class="row participate-comment">
	@include('doc.reader.comment')
</div>
@endif
<div id="participate-activity" class="row participate-activity">
	<h3>Comments</h3>
	<div class="activity-thread col-md-12">
    	<div id="@{{ 'comment_' + activity.id }}" class="row activity-item" ng-repeat="comment in comments | orderBy:activityOrder:true track by $id(comment)" ng-class="comment.label">
        	<div comment-item activity-item-link="@{{ activity.link }}"></div>
    	</div>
	</div>
</div>

