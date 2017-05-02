<div class="col-md-3">
    <div class="list-group">
        <a href="{{ route('sponsors.documents.index', $sponsor) }}"
            class="list-group-item {{ active(['sponsors.documents.index']) }}">

            @lang('messages.document.list')
        </a>
        <a href="{{ route('sponsors.members.index', $sponsor) }}"
            class="list-group-item {{ active(['sponsors.members.index']) }}">

            @lang('messages.sponsor.members')
        </a>
        @if ($sponsor->isSponsorOwner(Auth::user()->id) || Auth::user()->isAdmin())
            <a href="{{ route('sponsors.edit', $sponsor) }}"
                class="list-group-item {{ active(['sponsors.edit']) }}">

                @lang('messages.settings')
            </a>
        @endif
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <h4><small>@lang('messages.sponsor.create_another_header')</small></h4>
            <p><small>@lang('messages.sponsor.create_another_body')</small></p>
            {{ Html::linkRoute('sponsors.create', trans('messages.sponsor.create_another'), [], ['class' => 'btn btn-default btn-xs'])}}
        </div>
    </div>
</div>
