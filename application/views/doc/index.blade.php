@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<h1>Docs</h1>
<div class="row-fluid well well-large">
	<div class="span12">
		@foreach( $docs as $doc )
			<a href="{{ URL::to('doc/' . $doc->slug) }}">{{ $doc->title }}</a>
		@endforeach
	</div>
</div>
@endsection