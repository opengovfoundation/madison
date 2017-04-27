<div class="row">
    <div class="col-md-6">
        <h2 id="how-it-works">@lang('messages.home.how_it_works.title')</h2>
        <ol class="lead">
            <li>{!! trans('messages.home.how_it_works.step1') !!}</li>
            <li>{!! trans('messages.home.how_it_works.step2') !!}</li>
            <li>{!! trans('messages.home.how_it_works.step3') !!}</li>
        </ol>

        <h2><small>@lang('messages.home.sponsor_cta.title')</small></h2>
        <p>
            {!! trans('messages.home.sponsor_cta.text') !!}
            <strong>
                <a href="{{ route('sponsors.create') }}">@lang('messages.home.sponsor_cta.action_text')</a>
            </strong>
        </p>
    </div>
    <div class="col-md-6">
        <div class="embed-responsive embed-responsive-4by3">
            <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/69pPKZeKC8U" allowfullscreen></iframe>
        </div>
    </div>
</div>
