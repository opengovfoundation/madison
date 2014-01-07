@extends('layouts/main')
@section('content')
	@if(Auth::check())
	<input type="hidden" id="current_user" value="{{ Auth::user()->email }}" />
	<input type="hidden" id="current_user_id" value="{{ Auth::user()->id }}" />
	<input type="hidden" id="current_user_level" value="{{ Auth::user()->user_level }}" />
	<input type="hidden" id="current_user_name" value="{{ Auth::user()->fname . ' ' . substr(Auth::user()->lname, 0, 1) }}" />
	@endif
	{{ HTML::style('vendor/annotator/annotator.min.css') }}
	{{ HTML::script('vendor/annotator/annotator-full.min.js') }}
	{{ HTML::script('vendor/showdown/showdown.js') }}
	{{ HTML::script('js/doc.js') }}
	<div id="content" class="col-md-8 content doc_content @if(Auth::check())logged_in@endif">
		<div class="row">
			<div class="col-md-12">
				<h1>{{ $doc->title }}</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">{{ $doc->get_content('html') }}</div>
		</div>
	</div>
	<div class="col-md-3 col-md-offset-1 rightbar participate">
		@include('doc.reader.participate')
	</div>
</div>
@endsection