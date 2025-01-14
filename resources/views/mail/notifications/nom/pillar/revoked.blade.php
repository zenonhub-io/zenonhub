@component('mail::message')
# {{ $pillar->name }} has been revoked

@component('mail::button', ['url' => $link])
    View pillar
@endcomponent
@endcomponent
