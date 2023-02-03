@component('mail::message')
# A new project has been submitted!

## {{ $project->name }}

{{ $project->description }}

@component('mail::button', ['url' => route('az.project', ['hash' => $project->hash])])
View project
@endcomponent
@endcomponent
