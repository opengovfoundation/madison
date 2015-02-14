@extends('layouts/main')
@section('content')
	<div class="content col-md-12 docs">
		<h1>{{ trans('messages.alldocs') }}</h1>
		<ul>
			@foreach( $docs as $doc )
				<li>
					<a href="{{ URL::to('docs/' . $doc->slug) }}">{{ $doc->title }}</a>
				</li>
			@endforeach
		</ul>
	</div>
@endsection