@component('mail::message')
# {{ $project->name }} still needs votes

{{ $project->name }} only has one day left before it closes, you can still vote from:

@foreach($pillars as $pillar)
**{{ $pillar->name }}**
@endforeach

Current votes\
Yes: **{{ $project->votes()->where('is_yes', '1')->count() }}**\
No: **{{ $project->votes()->where('is_no', '1')->count() }}**\
Abstain: **{{ $project->votes()->where('is_abstain', '1')->count() }}**

@component('mail::button', ['url' => route('az.project', [
	'hash' => $project->hash,
	'utm_source' => 'notifications',
	'utm_medium' => 'email'
])])
View project
@endcomponent
@endcomponent
