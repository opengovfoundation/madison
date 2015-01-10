@extends('layouts/main')
@section('content')
	<div class="content col-md-12 docs">
		<h1>{{ trans('messages.searchresults') }}</h1>
		@if(count($results) == 0)
			<p>{{ trans('messages.nodocsmatched') }}:</p>
			<blockquote>&quot;{{ $query }}&quot;</blockquote>
		@else
			<p>{{ trans('messages.docsmatching') }}:</p>
			<blockquote>&quot;{{ $query }}&quot;</blockquote>
			@foreach( $results as $result )
				<a href="{{ URL::to('docs/' . $result->slug) }}">{{ $result->title }}</a>
			@endforeach
		@endif
	</div>
@endsection