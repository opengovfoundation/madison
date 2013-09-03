@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
	<div class="content col-md-12 docs">
		<h1>Docs</h1>
		@foreach( $docs as $doc )
			<a href="{{ URL::to('doc/' . $doc->slug) }}">{{ $doc->title }}</a>
		@endforeach
	</div>
@endsection