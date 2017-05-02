<table class="table">
    <thead>
        <tr>
            <th>@lang('messages.sponsor.name')</th>
            <th>@lang('messages.created')</th>
            <th>@lang('messages.sponsor.status')</th>
            <th>@lang('messages.sponsor.members')</th>
            <th>@lang('messages.document.list')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($sponsors as $sponsor)
            <tr>
                <td>
                    <a href="{{ route('sponsors.documents.index', $sponsor) }}">
                        {{ $sponsor->name }}
                    </a>
                </td>
                <td>
                    @include('components/date', [ 'datetime' => $sponsor->created_at, ])
                </td>
                <td>
                    @if (Auth::user()->isAdmin())
                        {{ Form::open(['route' => ['admin.sponsors.status.update', $sponsor->id], 'method' => 'put']) }}
                            {{ Form::select(
                                'status',
                                 collect(App\Models\Sponsor::getStatuses())->mapWithKeys_v2(function ($item) {
                                     return [$item => trans('messages.sponsor.statuses.'.$item)];
                                 })->toArray(),
                                 $sponsor->status,
                                 [
                                     'onchange' => 'if (this.selectedIndex >= 0) this.form.submit();',
                                     'class' => 'no-select2',
                                 ]
                                 )
                            }}
                        {{ Form::close() }}
                    @else
                        {{ trans('messages.sponsor.statuses.'.$sponsor->status) }}
                    @endif
                </td>
                <td>{{ $sponsor->members()->count() }}</td>
                <td>{{ $sponsor->docs()->count() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="text-center">
    @include('components.pagination', ['collection' => $sponsors])
</div>
