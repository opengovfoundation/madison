@extends('notification.base-html')
@section('content')
    <p>Congratulations! {{ $user->display_name }} has marked your {{ $label }} on <a href="{{ $doc->getLink() }}">{{ $doc->title }}</a> as seen:</p>

    <blockquote>
        <p>{{ $feedback->text }}</p>
    </blockquote>

    <p>Keep up the good work!</p>

    <a href="{{ $feedback->getLink() }}">Jump back in to the conversation</a>
@endsection
