@extends('notification.base-html')
@section('content')
    <p>The {{ $group->getDisplayName() }} group has been created. Please <a href="{{ url('/administrative-dashboard/verify-group') }}">verify the group</a>.</p>
@endsection
