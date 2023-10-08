@component('mail::message')
    # {{ $token->name }} has been created

    @component('mail::button', ['url' => route('explorer.token', [
        'zts' => $token->token_standard,
        'utm_source' => 'notifications',
        'utm_medium' => 'email'
    ])])
        View token
    @endcomponent
@endcomponent
