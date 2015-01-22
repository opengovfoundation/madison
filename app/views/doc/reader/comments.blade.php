@if(Auth::check())
  <div id="participate-comment" class="participate-comment">
  	@include('doc.reader.comment')
  </div>
  @if(Auth::user()->hasRole('Admin'))
    <div ng-init="admin=true"></div>
  @endif
  @else
  <div id="participate-comment" class="participate-comment">
  	<p>{{ trans('messages.please') }} <a href="{{ url('/user/login', $parameters = array(), $secure = null) }}" target="_self">{{ trans('messages.login') }}</a> {{ trans('messages.tocomment') }}.</p>
  </div>
  @endif
  <div id="participate-activity" class="participate-activity">
  	<h3>{{ trans('messages.comments') }}</h3>
  	<div class="activity-thread">
      <div id="@{{ 'comment_' + comment.id }}" class="activity-item" ng-repeat="comment in comments track by $id(comment)" ng-class="comment.label" ng-show="admin || comment.visiblec===1">
        <div comment-item activity-item-link="@{{ comment.link }}"></div>
      </div>
  	</div>
  </div>