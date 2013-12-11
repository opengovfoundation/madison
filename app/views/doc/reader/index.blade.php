@extends('layouts/main')
@section('content')
	<div class="col-md-8 content doc_content @if(Auth::check())logged_in@endif">
		<div class="row">
			<div class="col-md-12">
				<h1>{{ $doc->title }}</h1>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 markdown"
				>@foreach($doc->get_root_content() as $root_content){{{
					$root_content->content
				}}}@endforeach</div>
		</div>
	</div>
	<div class="col-md-3 col-md-offset-1 rightbar participate">
		@include('doc.reader.participate')
	</div>
</div>
{{ HTML::script('js/reader.js') }}
{{ HTML::script('js/bill-reader.js') }}
@endsection