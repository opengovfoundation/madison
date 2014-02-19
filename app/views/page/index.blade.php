@extends('layouts.main')
@section('content')
	<div class="content col-md-12">
		@include('page.' . $page_id)
	</div>
@endsection