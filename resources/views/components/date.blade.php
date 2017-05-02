<time datetime="{{ $datetime->toIso8601String() }}"
      title="{{$datetime->toIso8601String()}}">
    {{ $datetime->format(isset($format) ? $format : config('madison.date_format')) }}
</time>
