@component('mail::message')
# {{ $pillar->name }} lost a delegator!

[{{ $account->address }}]({{ route('explorer.account', ['address' => $account->address]) }}) stopped delegating to you and removed {{ $account->display_znn_balance }} ZNN from your weight.

Current rank: **{{ $pillar->rank }}**\
Total weight: **{{ $pillar->display_weight }}**\
Total delegators: **{{ $pillar->active_delegators_count }}**

@component('mail::button', ['url' => route('pillars.detail', ['slug' => $pillar->slug])])
View pillar
@endcomponent
@endcomponent
