@php
$labelSrOnly = false;
if (!empty($attributes['label-sr-only'])) {
  $labelSrOnly = true;
  unset($attributes['label-sr-only']);
}
@endphp

<div class="form-group">
    {{ Form::label($name, $displayName, ['class' => 'control-label' . ($labelSrOnly ? ' sr-only' : '')]) }}
    {{ Form::select($name, $list, $selected ?: request()->input(trim($name, "[]"), null), array_merge(['class' => 'form-control', 'autocomplete' => 'off'], $attributes)) }}
    @if (!empty($helpText))
        <p class="help-block small">{{ $helpText }}</p>
    @endif
</div>
