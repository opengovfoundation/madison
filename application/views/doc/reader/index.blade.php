@layout('layouts/main')
@section('nav')
@parent
@endsection
@section('content')
<div class="row-fluid">
	<div class="span12">
		<h1>{{ $doc->title }}</h1>
	</div>
</div>
<div class="row-fluid">
	<div class="span2 spacer"></div>
	<div class="span6 content doc_content @if(Auth::check())logged_in@endif">
		@foreach($doc->get_root_content() as $root_content)
			<?php Helpers::output_tree($root_content); ?>
		@endforeach
	</div>
	<div class="span3 rightbar participate">
		@include('doc.reader.participate')
	</div>
	<div class="span1 spacer"></div>
</div>
@endsection