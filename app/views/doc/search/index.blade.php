@extends('layouts/main')
@section('content')
	<div class="content col-md-12 docs">
		<h1>Search Results</h1>
		<p>Documents Matching:</p>
		<blockquote>&quot;{{ $query }}&quot;</blockquote>
		@foreach( $results as $result )
			<a href="{{ URL::to('docs/' . $result->slug) }}">{{ $result->title }}</a>
		@endforeach
	</div>
@endsection