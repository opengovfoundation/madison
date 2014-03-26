@extends('layouts/main')
@section('content')	
	@if(Auth::check())
		<script>
			var user = {
				id: {{ Auth::user()->id }},
				email: '{{ Auth::user()->email }}',
				user_level: {{ Auth::user()->user_level }},
				name: '{{ Auth::user()->fname . ' ' . substr(Auth::user()->lname, 0, 1) }}'
			};
		</script>
	@else
		<script>
			var user = {
				id: '',
				email: '',
				user_level: '',
				name: ''
			}
		</script>
	@endif
	<script>
		var doc = {{ $doc->toJSON() }}
	</script>
	{{ HTML::style('vendor/annotator/annotator.min.css') }}
	{{ HTML::script('vendor/annotator/annotator-full.min.js') }}
	{{ HTML::script('vendor/showdown/showdown.js') }}
	{{ HTML::script('js/annotator-madison.js') }}
	{{ HTML::script('js/doc.js') }}
	<div class="col-md-8" ng-controller="ReaderController">
		<div class="doc-info row">
			<h1>{{ $doc->title }}</h1>
			<div class="doc-sponsor" ng-repeat="sponsor in doc.sponsor">
				<strong>Sponsored by </strong><span><% sponsor.fname %> <% sponsor.lname %></span>
			</div>
			<div class="doc-status" ng-repeat="status in doc.statuses">
				<strong>Status: </strong><span><% status.label %></span>
			</div>
			<div class="doc-date" ng-repeat="date in doc.dates">
				<strong><% date.label %>: </strong><span><% date.date | parseDate | date:'shortDate' %></span>
			</div>
		</div>
		<div id="content" class="row content doc_content @if(Auth::check())logged_in@endif">
			<div id="doc_content" class="col-md-12">{{ $doc->get_content('html') }}</div>
		</div>
	</div>
	<div ng-controller="ParticipateController" ng-init="init({{ $doc->id }})" class="col-md-3 col-md-offset-1 rightbar participate">
		@include('doc.reader.participate')
	</div>
</div>
@endsection