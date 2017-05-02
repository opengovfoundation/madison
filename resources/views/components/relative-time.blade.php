{{-- TODO: the title content should be set to users or sites preferred display format --}}
{{-- TODO: only use diffForHumans if the time is fairly recent, otherwise display a more precise date --}}
<time datetime="{{ $datetime->toIso8601String()}}" title="{{ $datetime->toIso8601String() }}">{{ $datetime->diffForHumans() }}</time>
