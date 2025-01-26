@component('mail::message')
# New phase added to {{ $phase->project->name }}!

## {{ $phase->name }}

{{ $phase->description }}

@component('mail::button', ['url' => $link])
    View phase
@endcomponent
@endcomponent
