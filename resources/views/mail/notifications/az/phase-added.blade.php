@component('mail::message')
# New phase added to {{ $phase->project->name }}!

## {{ $phase->name }}

{{ $phase->description }}

@component('mail::button', ['url' => route('az.phase', ['hash' => $phase->hash])])
View phase
@endcomponent
@endcomponent
