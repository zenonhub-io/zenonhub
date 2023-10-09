@component('mail::message')
# A new token has been created!

{{ $token->name }} ({{$token->symbol}}) was issued by [{{ $token->owner->custom_label }}]({{route('explorer.account', [
    'address' => $token->owner->address,
    'utm_source' => 'notifications',
    'utm_medium' => 'email'
])}})

@component('mail::button', ['url' => route('explorer.token', [
    'zts' => $token->token_standard,
    'utm_source' => 'notifications',
    'utm_medium' => 'email'
])])
View token
@endcomponent
@endcomponent
