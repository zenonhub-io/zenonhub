<?php

namespace App\Http\Livewire\Stats\Bridge;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\BridgeWrap;
use App\Models\Nom\Orchestrator;
use App\Services\BitQuery;
use App\Services\BridgeStatus;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Number;
use Livewire\Component;

class Overview extends Component
{
    public ?array $liquidityData;

    public ?array $overview;

    public ?string $dateRange = null;

    public function render()
    {
        $bridgeStatus = App::make(BridgeStatus::class);
        $adminAccount = BridgeAdmin::getActiveAdmin()->account;
        $onlineOrchestrators = Orchestrator::getOnlinePercent();
        $requiredOrchestrators = Orchestrator::getRequiredOnlinePercent();

        return view('livewire.stats.bridge.overview', [
            'adminAddress' => $adminAccount,
            'halted' => $bridgeStatus->getIsHalted(),
            'estimatedUnhaltMonemtum' => $bridgeStatus->getEstimatedUnhaltMonemtum(),
            'onlineOrchestrators' => number_format($onlineOrchestrators),
            'requiredOrchestrators' => number_format($requiredOrchestrators),
            'affiliateLink' => config('zenon.bridge.affiliate_link'),
            'timeChallenges' => collect($bridgeStatus->getTimeChallenges())->where('isActive', true),
        ]);
    }

    public function setDateRange($range)
    {
        $this->dateRange = null;

        if ($range === 'day') {
            $this->dateRange = now()->subDay();
        }

        if ($range === 'week') {
            $this->dateRange = now()->subWeek();
        }

        if ($range === 'month') {
            $this->dateRange = now()->subDays(30);
        }

        if ($range === 'year') {
            $this->dateRange = now()->subYear();
        }

        $this->loadOverviewData();
    }

    public function loadOverviewData(): void
    {
        $znnToken = znn_token();
        $qsrToken = qsr_token();
        $bridgeAccount = Account::findByAddress(Account::ADDRESS_BRIDGE);

        //
        // Totals

        $znnVolume = AccountBlock::involvingAccount($bridgeAccount)
            ->createdLast($this->dateRange)
            ->where('token_id', $znnToken->id)
            ->sum('amount');
        $znnVolume = $znnToken->getDisplayAmount($znnVolume, 2, '.', '');

        $qsrVolume = AccountBlock::involvingAccount($bridgeAccount)
            ->createdLast($this->dateRange)
            ->where('token_id', $qsrToken->id)
            ->sum('amount');
        $qsrVolume = $qsrToken->getDisplayAmount($qsrVolume, 2, '.', '');

        $totalInbound = BridgeUnwrap::whereNotAffiliateReward()->createdLast($this->dateRange)->count();
        $totalOutbound = BridgeWrap::createdLast($this->dateRange)->count();

        //
        // Znn

        $inboundZnn = BridgeUnwrap::createdLast($this->dateRange)
            ->whereNotAffiliateReward()
            ->where('token_id', $znnToken->id)
            ->sum('amount');
        $inboundZnn = $znnToken->getDisplayAmount($inboundZnn, 2, '.', '');

        $outboundZnn = BridgeWrap::createdLast($this->dateRange)
            ->where('token_id', $znnToken->id)
            ->sum('amount');
        $outboundZnn = $znnToken->getDisplayAmount($outboundZnn, 2, '.', '');

        //
        // QSR

        $inboundQsr = BridgeUnwrap::createdLast($this->dateRange)
            ->whereNotAffiliateReward()
            ->where('token_id', $qsrToken->id)
            ->sum('amount');
        $inboundQsr = $qsrToken->getDisplayAmount($inboundQsr, 2, '.', '');

        $outboundQsr = BridgeWrap::createdLast($this->dateRange)
            ->where('token_id', $qsrToken->id)
            ->sum('amount');
        $outboundQsr = $qsrToken->getDisplayAmount($outboundQsr, 2, '.', '');

        //
        // Affiliate

        $affiliateTxCount = BridgeUnwrap::createdLast($this->dateRange)
            ->whereAffiliateReward()
            ->count();

        $affiliateZnn = BridgeUnwrap::createdLast($this->dateRange)
            ->whereAffiliateReward()
            ->where('token_id', $znnToken->id)
            ->sum('amount');
        $affiliateZnn = $znnToken->getDisplayAmount($affiliateZnn, 2, '.', '');

        $affiliateQsr = BridgeUnwrap::createdLast($this->dateRange)
            ->whereAffiliateReward()
            ->where('token_id', $qsrToken->id)
            ->sum('amount');
        $affiliateQsr = $qsrToken->getDisplayAmount($affiliateQsr, 2, '.', '');

        $this->overview = [
            'znnVolume' => $this->numberAbbreviator($znnVolume),
            'qsrVolume' => $this->numberAbbreviator($qsrVolume),
            'inboundTx' => $this->numberAbbreviator($totalInbound),
            'outboundTx' => $this->numberAbbreviator($totalOutbound),

            'inboundZnn' => $this->numberAbbreviator($inboundZnn),
            'outboundZnn' => $this->numberAbbreviator($outboundZnn),
            'netFlowZnn' => $this->numberAbbreviator($inboundZnn - $outboundZnn),

            'inboundQsr' => $this->numberAbbreviator($inboundQsr),
            'outboundQsr' => $this->numberAbbreviator($outboundQsr),
            'netFlowQsr' => $this->numberAbbreviator($inboundQsr - $outboundQsr),

            'affiliateTx' => $this->numberAbbreviator($affiliateTxCount),
            'affiliateZnn' => $this->numberAbbreviator($affiliateZnn),
            'affiliateQsr' => $this->numberAbbreviator($affiliateQsr),
        ];
    }

    private function loadLiquidityData(): void
    {
        $bitQuery = App::make(BitQuery::class);
        $data = $bitQuery->getLiquidityData();

        $poolData = collect($data['address'][0]['balances']);
        $pooledZnn = $poolData->where('currency.symbol', 'wZNN')->pluck('value')->first();
        $pooledEth = $poolData->where('currency.symbol', 'WETH')->pluck('value')->first();
        $pooledZnnValue = ($pooledZnn * znn_price());
        $pooledEthValue = ($pooledEth * eth_price());
        $totalLiquidity = ($pooledZnnValue + $pooledEthValue);

        $znnFormatter = $ethFormatter = $liquidityFormatter = 'format';

        if ($totalLiquidity > 100000) {
            $liquidityFormatter = 'abbreviate';
        }

        if ($pooledZnn > 10000) {
            $znnFormatter = 'abbreviate';
        }

        if ($pooledEth > 10000) {
            $ethFormatter = 'abbreviate';
        }

        $this->liquidityData = [
            'totalLiquidity' => Number::{$liquidityFormatter}($totalLiquidity, 2),
            'pooledWznn' => Number::{$znnFormatter}($pooledZnn, 2),
            'pooledWeth' => Number::{$ethFormatter}($pooledEth, 2),
        ];
    }

    private function numberAbbreviator(mixed $number, int $limit = 10000, int $prevision = 0): string
    {
        if ($number > $limit || ($number < 0 && abs($number) > $limit)) {
            return Number::abbreviate($number, 2);
        }

        return Number::format($number, $prevision);
    }
}
