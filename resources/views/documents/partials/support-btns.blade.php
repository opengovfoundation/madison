@if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
    <div class="support-btns pull-right text-center">
        <div>
            <small>@lang('messages.document.support_prompt')</small>
        </div>
        <div class="btn-group support-btn" role="group">
                {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                    <input type="hidden" name="support" value="1">

                    <button type="submit" class="btn btn-primary btn-xs {{ $userSupport === true ? 'active' : '' }}">
                        <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                        @if ($userSupport === true)
                            {{ trans('messages.document.supported') }}
                        @else
                            {{ trans('messages.document.support') }}
                        @endif
                        ({{ $supportCount }})
                    </button>
                {{ Form::close() }}
        </div>
        <div class="btn-group oppose-btn" role="group">
                {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                    <input type="hidden" name="support" value="0">
                    <button type="submit" class="btn btn-primary btn-xs {{ $userSupport === false ? 'active' : '' }}">
                        <i class="fa fa-thumbs-down" aria-hidden="true"></i>
                        @if ($userSupport === false)
                                {{ trans('messages.document.opposed') }}
                        @else
                                {{ trans('messages.document.oppose') }}
                        @endif
                        ({{ $opposeCount }})
                    </button>
                {{ Form::close() }}
        </div>
    </div>
@endif
