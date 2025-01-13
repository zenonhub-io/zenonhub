@component('mail::message')
# A new project has been submitted!

## {{ $project->name }}

{{ $project->description }}

@component('mail::button', ['url' => route('accelerator-z.project.detail', [
    'hash' => $project->hash,
    'utm_source' => 'notifications',
    'utm_medium' => 'email'
])])
    View project
@endcomponent
@endcomponent
