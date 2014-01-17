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
		var doc = {
			slug: '{{ $doc->slug }}',
			id: {{ $doc->id }}
		}
	</script>
	{{ HTML::style('vendor/annotator/annotator.min.css') }}
	{{ HTML::script('vendor/annotator/annotator-full.min.js') }}
	{{ HTML::script('vendor/showdown/showdown.js') }}
	{{ HTML::script('js/annotator-madison.js') }}
	{{ HTML::script('js/doc.js') }}
	<div id="content" ng-controller="ReaderCtrl" class="col-md-8 content doc_content @if(Auth::check())logged_in@endif">
		<div class="row">
			<div class="col-md-12">
				<h1>{{ $doc->title }}</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">{{ $doc->get_content('html') }}</div>
		</div>
	</div>
	<div ng-controller="ParticipateCtrl" class="col-md-3 col-md-offset-1 rightbar participate">
		@include('doc.reader.participate')
	</div>
</div>
@endsection