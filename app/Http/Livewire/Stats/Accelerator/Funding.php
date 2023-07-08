<?php

namespace App\Http\Livewire\Stats\Accelerator;

use App\Http\Livewire\ChartTrait;
use App\Models\Nom\Account;
use Livewire\Component;

class Funding extends Component
{
    use ChartTrait;

    public Account $acceleratorContract;

    public bool $readyToLoad = false;

    public function render()
    {
        return view('livewire.stats.accelerator.funding');
    }

    public function loadFundingData()
    {
        $this->readyToLoad = true;

        $this->acceleratorContract = Account::findByAddress(Account::ADDRESS_ACCELERATOR);
        $znnToken = znn_token();
        $qsrToken = qsr_token();

        $totalZnnUsed = $this->acceleratorContract
            ->sent_blocks()
            ->where('token_id', $znnToken->id)
            ->sum('amount');

        $totalQsrUsed = $this->acceleratorContract
            ->sent_blocks()
            ->where('token_id', $qsrToken->id)
            ->sum('amount');

        $fundingLabels = [
            'Remaining',
            'Used',
        ];

        $znnFunds = [
            'labels' => $fundingLabels,
            'data' => [
                (float) filter_var($this->acceleratorContract->display_znn_balance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                (float) filter_var($znnToken->getDisplayAmount($totalZnnUsed), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            ],
        ];

        $qsrFunds = [
            'labels' => $fundingLabels,
            'data' => [
                (float) filter_var($this->acceleratorContract->display_qsr_balance, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                (float) filter_var($qsrToken->getDisplayAmount($totalQsrUsed), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            ],
        ];

        $this->emit('stats.az.fundingDataLoaded', [
            'znn' => $znnFunds,
            'qsr' => $qsrFunds,
        ]);
    }
}
