@if ('checkbox' === $type)
    <div class="checkbox">
        {{ Form::label($name, Form::checkbox($name, $value ?: $request()->input($name, null)) . $displayName, [], false) }}
    </div>
@else
    <div class="form-group">
        {{ Form::label($name, $displayName, ['class' => 'control-label']) }}
        {{ Form::input($type, $name, $value ?: request()->input($name, null), array_merge(['type' => $type, 'class' => 'form-control'], $attributes)) }}
        @if (!empty($helpText))
            <p class="help-block">{{ $helpText }}</p>
        @endif
    </div>
@endif
