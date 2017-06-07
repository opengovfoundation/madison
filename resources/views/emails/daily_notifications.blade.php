@component('mail::message')

@lang('messages.notifications.frequencies.' . App\Models\NotificationPreference::FREQUENCY_DAILY . '.intro')

@foreach ($groupedAndFormattedNotifications as $notification)
- {!! $notification !!}
@endforeach

@lang('messages.notifications.salutation', ['name' => config('app.name')])

@component('mail::subcopy')
{!! $unsubscribeMarkdown !!}
@endcomponent

@endcomponent
