@component('mail::message')
# {{ $pillar->name }} has been registered

Current rewards:
### Momentum {{ $pillar->give_momentum_reward_percentage }}%
### Delegation {{ $pillar->give_delegate_reward_percentage }}%

@component('mail::button', ['url' => route('pillars.detail', ['slug' => $pillar->slug])])
View pillar
@endcomponent
@endcomponent
