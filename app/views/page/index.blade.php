@extends('layouts.main')
@section('content')
	<div class="content col-md-8">
		@include('page.' . $page_id)
	</div>
	@foreach($rightbar as $right_widget)
		@include('partials.rightbar.' . $right_widget)
	@endforeach
@endsection