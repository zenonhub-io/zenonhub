@component('mail::message')
# A new project has been submitted!

## {{ $project->name }}

{{ $project->description }}

@component('mail::button', ['url' => $link])
    View project
@endcomponent
@endcomponent
