@component('mail::message')
# {{ $pillar->name }} gained a delegator!

[{{ $account->address }}]({{ route('explorer.account', ['address' => $account->address]) }}) started delegating to you and added {{ $account->display_znn_balance }} ZNN to your weight.

Current rank: **{{ $pillar->rank }}**\
Total weight: **{{ $pillar->display_weight }}**\
Total delegators: **{{ $pillar->active_delegators_count }}**

@component('mail::button', ['url' => route('pillars.detail', ['slug' => $pillar->slug])])
View pillar
@endcomponent
@endcomponent
