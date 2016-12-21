@php ($savedValue = $value ?: request()->input($name, null))

@if ('checkbox' === $type)
    <div class="checkbox">
        <label>
            {{ Form::checkbox($name, $savedValue, null, $attributes) }}
            {{ $displayName }}
        </label>
    </div>
@else
    <div class="form-group">
        {{ Form::label($name, $displayName, ['class' => 'control-label']) }}

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
