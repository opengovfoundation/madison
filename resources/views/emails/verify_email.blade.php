@php ($url = route('users.verify_email', [$user, $user->token]))

@component('mail::message')
# @lang('messages.email_verification.ask')

@lang('messages.email_verification.reason')

@component('mail::button', ['url' => $url])
@lang('messages.email_verification.action')
@endcomponent

@lang('messages.notifications.salutation', ['name' => config('app.name')])

@component('mail::subcopy')
@lang('messages.notifications.having_trouble', ['actionText' => trans('messages.email_verification.action')])
 [{{ $url }}]({{ $url }})
@endcomponent
@endcomponent
