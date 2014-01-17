@extends('layouts/main')
@section('content')
	<div class="content col-md-12 docs">
		<h1>All Documents</h1>
		@foreach( $docs as $doc )
			<a href="{{ URL::to('docs/' . $doc->slug) }}">{{ $doc->title }}</a>
		@endforeach
	</div>
@endsection