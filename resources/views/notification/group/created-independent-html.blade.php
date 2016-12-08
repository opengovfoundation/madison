@extends('notification.base-html')
@section('content')
    <p>{{ $group->getDisplayName() }} has requested independent sponsorship status. Please <a href="{{ url('/administrative-dashboard/verify-sponsors') }}">verify the sponsor request</a>.</p>
@endsection
