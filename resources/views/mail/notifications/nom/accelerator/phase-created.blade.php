@component('mail::message')
# New phase added to {{ $phase->project->name }}!

## {{ $phase->name }}

{{ $phase->description }}

@component('mail::button', ['url' => route('az.phase', [
    'hash' => $phase->hash,
    'utm_source' => 'notifications',
    'utm_medium' => 'email'
])])
    View phase
@endcomponent
@endcomponent
