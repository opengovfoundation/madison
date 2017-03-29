<div class="col-md-3">
    <div class="list-group">
        <a href="{{ route('documents.manage.settings', $document) }}"
            class="list-group-item {{ active(['documents.manage.settings']) }}">

            @lang('messages.settings')
        </a>
        <a href="{{ route('documents.manage.comments', $document) }}"
            class="list-group-item {{ active(['documents.manage.comments']) }}">

            @lang('messages.document.comments')
        </a>
    </div>
</div>
