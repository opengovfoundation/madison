@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level == 'error')
# @lang('messages.notifications.greeting.error')
@else
# @lang('messages.notifications.greeting.normal')
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@if (isset($actionText))
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endif

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

<!-- Salutation -->
@if (! empty($salutation))
{{ $salutation }}
@else
@lang('messages.notifications.salutation', ['name' => config('app.name')])
@endif

<!-- Subcopy -->
@if (isset($actionText))
@component('mail::subcopy')
@lang('messages.notifications.having_trouble', ['actionText' => $actionText])
 [{{ $actionUrl }}]({{ $actionUrl }})

@lang('messages.notifications.unsubscribe')
@endcomponent
@endif
@endcomponent
