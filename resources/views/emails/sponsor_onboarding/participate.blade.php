@component('mail::message')

@lang('messages.sponsor.onboarding.salutation', ['name' => $user->fname])


@lang('messages.sponsor.onboarding.participate.opening')


@includeLocale('sponsors.onboarding.$locale.participate')


# @lang('messages.sponsor.onboarding.learn_more')


@lang('messages.sponsor.onboarding.complete_guide', ['guideLink' => route('sponsors.guide')])
@endcomponent
