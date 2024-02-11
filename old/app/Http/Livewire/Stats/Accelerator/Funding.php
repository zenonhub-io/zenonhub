<?php

namespace App\Http\Livewire\Stats\Accelerator;

use App\Http\Livewire\ChartTrait;
use App\Models\Nom\Account;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Funding extends Component
{
    use ChartTrait;

    public Account $acceleratorContract;

    public function render()
    {
        return view('livewire.stats.accelerator.funding');
    }

    public function loadFundingData()
    {
        $this->acceleratorContract = Account::findByAddress(Account::ADDRESS_ACCELERATOR);
        $cacheExpiry = (60 * 60);
        $fundingLabels = [
            'Remaining',
            'Used',
        ];

        $znnFunds = Cache::remember('stats.az.znnFunding', $cacheExpiry, function () use ($fundingLabels) {
            $znnToken = znn_token();
            $totalZnnUsed = $this->acceleratorContract
                ->sent_blocks()
                ->where('token_id', $znnToken->id)
                ->sum('amount');

            return [
                'labels' => $fundingLabels,
                'data' => [
                    (float) filter_var($this->acceleratorContract->display_znn_balance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    (float) filter_var($znnToken->getDisplayAmount($totalZnnUsed), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                ],
            ];
        });

        $qsrFunds = Cache::remember('stats.az.qsrFunding', $cacheExpiry, function () use ($fundingLabels) {
            $qsrToken = qsr_token();
            $totalQsrUsed = $this->acceleratorContract
                ->sent_blocks()
                ->where('token_id', $qsrToken->id)
                ->sum('amount');

            return [
                'labels' => $fundingLabels,
                'data' => [
                    (float) filter_var($this->acceleratorContract->display_qsr_balance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                    (float) filter_var($qsrToken->getDisplayAmount($totalQsrUsed), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                ],
            ];
        });

        $this->emit('stats.az.fundingDataLoaded', [
            'znn' => $znnFunds,
            'qsr' => $qsrFunds,
        ]);
    }
}
