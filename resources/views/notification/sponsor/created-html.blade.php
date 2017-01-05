@extends('notification.base-html')
@section('content')
    <p>The {{ $sponsor['name'] }} sponsor has been created. Please <a href="{{ url('/administrative-dashboard/verify-sponsor') }}">verify the sponsor</a>.</p>
@endsection
