@component('mail::message')
# A new token has been created!

{{ $token->name }} ({{$token->symbol}}) was issued by {{ $token->owner->custom_label }}

@component('mail::button', ['url' => $link])
    View token
@endcomponent
@endcomponent
