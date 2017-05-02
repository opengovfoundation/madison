<ul class="media-list">
    @foreach ($comments as $comment)
        @include('documents.partials.comment-'.$view)
    @endforeach
</ul>
