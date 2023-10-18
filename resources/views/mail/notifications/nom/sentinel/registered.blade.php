@component('mail::message')
# A new sentinel has been registered!

@component('mail::button', ['url' => route('explorer.account', [
    'address' => $sentinel->owner->address,
    'utm_source' => 'notifications',
    'utm_medium' => 'email'
])])
View account
@endcomponent
@endcomponent
