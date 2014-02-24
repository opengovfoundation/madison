<!-- div.row -->
<div class="btn-group btn-group-justified">
	<a href="#" class="btn btn-default doc-support" ng-class="{'btn-success': supported}" ng-click="support(true, $event)">
		<span class="glyphicon glyphicon-ok"></span>
		&nbsp;&nbsp;SUPPORT
	</a>
	<a href="#" class="btn btn-default doc-oppose" ng-class="{'btn-danger': opposed}" ng-click="support(false, $event)">
		<span class="glyphicon glyphicon-remove"></span>
		&nbsp;&nbsp;OPPOSE
	</a>
</div>