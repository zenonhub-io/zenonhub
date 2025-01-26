@component('mail::message')
# {{ $pillar->name }} has been registered

Current rewards:
### Momentum {{ $pillar->momentum_rewards }}%
### Delegation {{ $pillar->delegate_rewards }}%

@component('mail::button', ['url' => $link])
    View pillar
@endcomponent
@endcomponent
