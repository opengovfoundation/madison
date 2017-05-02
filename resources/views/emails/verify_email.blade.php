@component('mail::message')
# @lang('messages.email_verification.ask')

@lang('messages.email_verification.reason')

@component('mail::button', ['url' => route('users.verify_email', [$user, $user->token])])
@lang('messages.email_verification.action')
@endcomponent

@lang('messages.notifications.salutation', ['name' => config('app.name')])
@endcomponent
