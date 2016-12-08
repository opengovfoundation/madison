@extends('notification.base-html')
@section('content')
    <p>{{ $subcomment->user->getDisplayName() }} commented on your <a href="{{ $parent->getLink() }}">{{ $parentType }}</a>:</p>
    <blockquote>{{ $subcomment->annotationType->content }}</blockquote>
    Jump back over to <a href="{{ $subcomment->getLink() }}">the conversation</a> to respond!
@endsection
