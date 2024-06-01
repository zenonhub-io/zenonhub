<?php

declare(strict_types=1);

namespace App\Http\Livewire\Stats\Accelerator;

use App\Domains\Nom\Enums\EmbeddedContractsEnum;
use App\Domains\Nom\Models\Account;
use App\Http\Livewire\ChartTrait;
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
        $this->acceleratorContract = Account::firstWhere('address', EmbeddedContractsEnum::ACCELERATOR->value);
        $cacheExpiry = (60 * 60);
        $fundingLabels = [
            'Remaining',
            'Used',
        ];

        $znnFunds = Cache::remember('stats.az.znnFunding', $cacheExpiry, function () use ($fundingLabels) {
            $znnToken = app('znnToken');
            $totalZnnUsed = $this->acceleratorContract
                ->sentBlocks()
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
            $qsrToken = app('qsrToken');
            $totalQsrUsed = $this->acceleratorContract
                ->sentBlocks()
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
