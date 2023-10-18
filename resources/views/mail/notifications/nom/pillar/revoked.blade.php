@component('mail::message')
# {{ $pillar->name }} has been revoked

@component('mail::button', ['url' => route('pillars.detail', [
	'slug' => $pillar->slug,
	'utm_source' => 'notifications',
	'utm_medium' => 'email'
])])
View pillar
@endcomponent
@endcomponent
