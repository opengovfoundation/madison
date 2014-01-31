<h3 class="rightbar-header">Notes</h3>
<div class="sidebar-annotation-wrapper">
	<div class="sidebar-annotation" ng-repeat="annotation in annotations">
		<blockquote>
			<a href="/note/<% annotation.id %>">
				<div class="annotation-content" ng-bind-html="annotation.html"></div><div class="annotation-author"><% annotation.user.name %></div>
			</a>
			<span class="sidebar-comment-label" ng-show="annotation.comments" ng-click="showCommentThread(annotation.id, $event)">Comments (<% comments.length %>)<span class="sidebar-comment-caret caret caret-right"></span></span>
		</blockquote>
		
		<div id="<% annotation.id %>-comments" class="sidebar-comment-thread collapse">
			<div class="sidebar-comment" ng-repeat="comment in annotation.comments">
				<blockquote>
					<span class="comment-content"><% comment.text %></span>
					<div class="comment-author"><% comment.user.name %></div>
				</blockquote>
			</div>
		</div>
	</div>
</div>