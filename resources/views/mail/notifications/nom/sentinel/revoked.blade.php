@component('mail::message')
# A sentinel has  been revoked!

@component('mail::button', ['url' => route('explorer.account', [
    'address' => $sentinel->owner->address,
    'utm_source' => 'notifications',
    'utm_medium' => 'email'
])])
View account
@endcomponent
@endcomponent
