@component('mail::message')
# {{ $pillar->name }} updated their rewards

Current rewards:\
**Momentum {{ $pillar->give_momentum_reward_percentage }}% | Delegation {{ $pillar->give_delegate_reward_percentage }}%**

@if ($pillar->previous_history)
Previously:\
**Momentum {{ $pillar->previous_history->give_momentum_reward_percentage }}% | Delegation {{ $pillar->previous_history->give_delegate_reward_percentage }}%**
@endif

@component('mail::button', ['url' => route('pillars.detail', ['slug' => $pillar->slug])])
View pillar
@endcomponent
@endcomponent
