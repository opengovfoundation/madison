<div class="row">
    <div class="col-xs-12 text-center">
        <div class="support-btns">
            @if ($document->discussion_state !== \App\Models\Doc::DISCUSSION_STATE_HIDDEN)
                <div class="support-btn" role="group">
                    {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                        <input type="hidden" name="support" value="1">

                        <button type="submit" class="btn support {{ $userSupport === true ? 'active' : '' }}"
                        {{ !$document->isDiscussionOpen() ? 'disabled' : '' }}>

                            <i class="fa fa-thumbs-up" aria-hidden="true"></i>
                            {{ trans('messages.document.support') }}
                            <span class="count">{{ $supportCount }}</span>
                        </button>
                    {{ Form::close() }}
                </div>
                <div class="separator"></div>
                <div class="oppose-btn" role="group">
                    {{ Form::open(['route' => ['documents.support', $document], 'method' => 'put']) }}
                        <input type="hidden" name="support" value="0">
                        <button type="submit" class="btn oppose {{ $userSupport === false ? 'active' : '' }}"
                        {{ !$document->isDiscussionOpen() ? 'disabled' : '' }}>
                            <i class="fa fa-thumbs-down" aria-hidden="true"></i>
                            {{ trans('messages.document.oppose') }}
                            <span class="count">{{ $opposeCount }}</span>
                        </button>
                    {{ Form::close() }}
                </div>
            @endif
        </div>
    </div>
</div>
