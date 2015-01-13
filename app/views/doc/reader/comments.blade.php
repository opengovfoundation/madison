@if(Auth::check())
	<div id="participate-comment" class="row participate-comment">
		@include('doc.reader.comment')
	</div>
	@if(Auth::user()->hasRole('Admin'))
		<div ng-init="admin=true"></div>
	@endif
@else
	<div id="participate-comment" class="row participate-comment">
		<p>Please <a href="{{ url('/user/login', $parameters = array(), $secure = null) }}" target="_self">login</a> to comment.</p>
	</div>
@endif
<div id="participate-activity" class="row participate-activity">
	<h3>Comments</h3>
	<div class="activity-thread col-md-12">
		<div ng-repeat="comment in comments track by $id(comment)" >
			<div id="@{{ 'comment_' + comment.id }}" class="row activity-item" ng-class="comment.label" ng-show="admin || comment.visiblec===1" >
				<div comment-item activity-item-link="@{{ comment.link }}"></div>
			</div>
		</div>
	</div>
</div>

