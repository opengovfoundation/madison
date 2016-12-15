<div class="form-group">
    {{ Form::label($name, $displayName, ['class' => 'control-label']) }}
    {{ Form::select($name, $list, $selected ?: request()->input(trim($name, "[]"), null), array_merge(['class' => 'form-control'], $attributes)) }}
    @if (!empty($helpText))
        <p class="help-block">{{ $helpText }}</p>
    @endif
</div>
