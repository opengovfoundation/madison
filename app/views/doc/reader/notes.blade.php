<h3 class="rightbar-header">Notes</h3>
<div class="sidebar-annotation-wrapper">
	<div class="sidebar-annotation" ng-repeat="annotation in annotations">
		<blockquote>
			<div class="row">
				<div class="col-md-3">
					<img ng-src="http://gravatar.com/avatar/<% annotation.user.email | gravatar %>" alt="" class="img-responsive">
				</div>
				<div class="col-md-9">
					<a href="/note/<% annotation.id %>">
						<div class="annotation-content" ng-bind-html="annotation.html"></div>
					</a>
					<div class="annotation-author">
						<a href="/user/<% annotation.user.id %>">
							<% annotation.user.name %>
						</a>
					</div>
				</div>
			</div>

			
			<span class="sidebar-comment-label" ng-show="annotation.comments" ng-click="showCommentThread(annotation.id, $event)">Comments (<% comments.length %>)<span class="sidebar-comment-caret caret caret-right"></span></span>
		</blockquote>
		
		<div id="<% annotation.id %>-comments" class="sidebar-comment-thread collapse">
			<div class="sidebar-comment" ng-repeat="comment in annotation.comments">
				<blockquote>
					<div class="row">
						<div class="col-md-2">
						</div>
						<div class="col-md-10">
							<span class="comment-content"><% comment.text %></span>
							<div class="comment-author">
								<a href="/user/<% comment.user.id %>">
									<% comment.user.name %>
								</a>
							</div>
						</div>
					</div>
				</blockquote>
			</div>
		</div>
	</div>
</div>