@layout('layouts.main')
@section('content')
	<div class="span2 spacer"></div>
	<div class="content span6">
		@include('page.' . $page_id)
	</div>
	@foreach($rightbar as $right_widget)
		@include('partials.rightbar.' . $right_widget)
	@endforeach
	<div class="span2 spacer"></div>
@endsection