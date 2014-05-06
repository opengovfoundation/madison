@extends('layouts/main')
@section('content')	
	@if(Auth::check())
		<script>
			var user = {
				id: {{ Auth::user()->id }},
				email: '{{ Auth::user()->email }}',
				name: '{{ Auth::user()->fname . ' ' . substr(Auth::user()->lname, 0, 1) }}'
			};
		</script>
	@else
		<script>
			var user = {
				id: '',
				email: '',
				name: ''
			}
		</script>
	@endif
	<script>
		var doc = {{ $doc->toJSON() }};
		@if($showAnnotationThanks)
			$.showAnnotationThanks = true;
		@else
			$.showAnnotationThanks = false;
		@endif
	</script>
	{{ HTML::style('vendor/annotator/annotator.min.css') }}
	{{ HTML::script('vendor/annotator/annotator-full.min.js') }}
	{{ HTML::script('vendor/showdown/showdown.js') }}
	{{ HTML::script('bower_components/bootstrap/js/collapse.js') }}
	{{ HTML::script('bower_components/bootstrap/js/modal.js') }}
	{{ HTML::script('js/annotator-madison.js') }}
	{{ HTML::script('js/doc.js') }}

<div class="modal fade" id="annotationThanks" tabindex="-1" role="dialog" aria-labelledby="annotationThanks" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
    </div>
  </div>
</div>
<div class="col-md-12">
	<div class="row">
		<div class="col-md-8" ng-controller="ReaderController" ng-init="init({{ $doc->id }})">
			<div class="doc-info row">
				<div class="col-md-12">
					<div class="row">
						<h1>{{ $doc->title }}</h1>
					</div>
					<div class="doc-sponsor row" ng-repeat="sponsor in doc.sponsor">
						<strong>Sponsored by </strong><span>@{{ sponsor.fname }} @{{ sponsor.lname }}</span>
					</div>
					<div class="doc-status row" ng-repeat="status in doc.statuses">
						<strong>Status: </strong><span>@{{ status.label }}</span>
					</div>
					<div class="doc-date row" ng-repeat="date in doc.dates">
						<strong>@{{ date.label }}: </strong><span>@{{ date.date | parseDate | date:'shortDate' }}</span>
					</div>
					<div class="row" ng-show="user.id > 0">
							<a href="#" class="btn btn-default doc-support" ng-click="support(true, $event)" ng-class="{'btn-success': supported}">Support This Document</a>
							<a href="#" class="btn btn-default doc-oppose" ng-click="support(false, $event)" ng-class="{'btn-danger': opposed}">Oppose This Document</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-8">
			<div id="content" class="row content doc_content @if(Auth::check())logged_in@endif">
				<div id="doc_content" class="col-md-12">{{ $doc->get_content('html') }}</div>
			</div>
		</div>
		<div ng-controller="ParticipateController" ng-init="init({{ $doc->id }})" class="col-md-3 col-md-offset-1 rightbar participate">
			@include('doc.reader.participate')
		</div>
	</div>
</div>
	
	
</div>
@endsection