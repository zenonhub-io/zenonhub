@component('mail::message')
# {{ $pillar->name }} has been revoked

@component('mail::button', ['url' => route('pillars.detail', ['slug' => $pillar->slug])])
View pillar
@endcomponent
@endcomponent
