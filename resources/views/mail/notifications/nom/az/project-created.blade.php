@component('mail::message')
# A new project has been submitted!

## {{ $project->name }}

{{ $project->description }}

@component('mail::button', ['url' => route('az.project', [
	'hash' => $project->hash,
	'utm_source' => 'notifications',
	'utm_medium' => 'email'
])])
View project
@endcomponent
@endcomponent
