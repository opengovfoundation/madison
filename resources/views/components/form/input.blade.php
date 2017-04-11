@php ($savedValue = $value ?: request()->input($name, null))

@php
$labelSrOnly = false;
if (!empty($attributes['label-sr-only'])) {
  $labelSrOnly = true;
  unset($attributes['label-sr-only']);
}
@endphp

@if ('checkbox' === $type)
    <div class="checkbox">
        <label>
            {{ Form::checkbox($name, $savedValue, !empty($savedValue), $attributes) }}
            {{ Form::hidden($name, $savedValue ?: "0") }}
            {{ $displayName }}
        </label>
    </div>
@else
    <div class="form-group">
        {{ Form::label($name, $displayName, ['class' => 'control-label' . ($labelSrOnly ? ' sr-only' : '')]) }}

        @if ('textarea' === $type)
            {{ Form::textarea($name, $savedValue, array_merge(['class' => 'form-control'], $attributes)) }}
        @else
            {{ Form::input($type, $name, $savedValue, array_merge(['type' => $type, 'class' => $type !== 'file' ? 'form-control' : ''], $attributes)) }}
        @endif

        @if (!empty($helpText))
            <p class="help-block">{{ $helpText }}</p>
        @endif
    </div>
@endif
