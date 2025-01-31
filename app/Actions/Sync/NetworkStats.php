<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\NetworkStatHistory;
use App\Models\Nom\Pillar;
use App\Models\Nom\Plasma;
use App\Models\Nom\Sentinel;
use App\Models\Nom\Stake;
use App\Models\Nom\Token;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class NetworkStats
{
    use AsAction;

    public string $commandSignature = 'sync:network-stats';

    public function handle(Carbon $date): void
    {
        NetworkStatHistory::updateOrCreate([
            'date' => $date->format('Y-m-d'),
        ], [
            'total_tx' => $this->getTotalTx($date, '<='),
            'total_daily_tx' => $this->getTotalTx($date),
            'total_addresses' => $this->getTotalAddresses($date),
            'total_daily_addresses' => $this->getTotalDailyAddresses($date),
            'total_active_addresses' => $this->getTotalActiveAddresses($date),
            'total_tokens' => $this->getTotalTokens($date, '<='),
            'total_daily_tokens' => $this->getTotalTokens($date),
            'total_stakes' => $this->getTotalStakes($date, '<='),
            'total_daily_stakes' => $this->getTotalStakes($date),
            'total_staked' => $this->getTotalStaked($date, '<='),
            'total_daily_staked' => $this->getTotalStaked($date),
            'total_fusions' => $this->getTotalFusions($date, '<='),
            'total_daily_fusions' => $this->getTotalFusions($date),
            'total_fused' => $this->getTotalFused($date, '<='),
            'total_daily_fused' => $this->getTotalFused($date),
            'total_pillars' => $this->getTotalPillars($date),
            'total_sentinels' => $this->getTotalSentinels($date),
        ]);
    }

    public function asCommand(Command $command): void
    {
        $period = CarbonPeriod::create(AccountBlock::min('created_at'), AccountBlock::max('created_at'));
        $progressBar = new ProgressBar(new ConsoleOutput, $period->count());
        $progressBar->start();

        foreach ($period as $date) {
            $this->handle($date);
            $progressBar->advance();
        }

        $progressBar->finish();
    }

    private function getTotalTx(Carbon $date, string $operator = '='): int
    {
        if ($operator === '=') {
            return AccountBlock::whereBetween('created_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ])->count();
        }

        return AccountBlock::where('created_at', '<=', $date->copy()->endOfDay())->count();
    }

    private function getTotalAddresses(Carbon $date): int
    {
        return Account::whereHas('sentBlocks', function ($query) use ($date) {
            $query->where('created_at', '<=', $date->copy()->endOfDay());
        })->orWhereHas('receivedBlocks', function ($query) use ($date) {
            $query->where('created_at', '<=', $date->copy()->endOfDay());
        })->count();
    }

    private function getTotalDailyAddresses(Carbon $date): int
    {
        return Account::whereBetween('first_active_at', [
            $date->copy()->startOfDay(),
            $date->copy()->endOfDay(),
        ])->count();
    }

    private function getTotalActiveAddresses(Carbon $date): int
    {
        return Account::whereHas('sentBlocks', function ($query) use ($date) {
            $query->whereBetween('created_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ]);
        })->count();
    }

    private function getTotalTokens(Carbon $date, string $operator = '='): int
    {
        if ($operator === '=') {
            return Token::whereBetween('created_at', [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
            ])->count();
        }

        return Token::where('created_at', '<=', $date->copy()->endOfDay())->count();
    }

    private function getTotalStakes(Carbon $date, string $operator = '='): int
    {
        $znnToken = app('znnToken');

        if ($operator === '=') {
            return Stake::where('token_id', $znnToken->id)
                ->whereActive()
                ->whereBetween('started_at', [
                    $date->copy()->startOfDay(),
                    $date->copy()->endOfDay(),
                ])
                ->count();
        }

        return Stake::where('token_id', $znnToken->id)
            ->whereActive()
            ->where('started_at', '<=', $date->copy()->endOfDay())
            ->count();
    }

    private function getTotalStaked(Carbon $date, string $operator = '='): mixed
    {
        $znnToken = app('znnToken');

        if ($operator === '=') {
            return Stake::where('token_id', $znnToken->id)
                ->whereActive()
                ->whereBetween('started_at', [
                    $date->copy()->startOfDay(),
                    $date->copy()->endOfDay(),
                ])
                ->sum('amount');
        }

        return Stake::where('token_id', $znnToken->id)
            ->whereActive()
            ->where('started_at', '<=', $date->copy()->endOfDay())
            ->sum('amount');
    }

    private function getTotalFusions(Carbon $date, string $operator = '='): int
    {
        if ($operator === '=') {
            return Plasma::whereActive()
                ->whereBetween('started_at', [
                    $date->copy()->startOfDay(),
                    $date->copy()->endOfDay(),
                ])
                ->count();
        }

        return Plasma::whereActive()
            ->where('started_at', '<=', $date->copy()->endOfDay())
            ->count();
    }

    private function getTotalFused(Carbon $date, string $operator = '='): mixed
    {
        if ($operator === '=') {
            return Plasma::whereActive()
                ->whereBetween('started_at', [
                    $date->copy()->startOfDay(),
                    $date->copy()->endOfDay(),
                ])
                ->sum('amount');
        }

        return Plasma::whereActive()
            ->where('started_at', '<=', $date->copy()->endOfDay())
            ->sum('amount');
    }

    private function getTotalPillars(Carbon $date): int
    {
        return Pillar::whereActive()
            ->where('created_at', '<=', $date->copy()->endOfDay())
            ->count();
    }

    private function getTotalSentinels(Carbon $date): int
    {
        return Sentinel::whereActive()
            ->where('created_at', '<=', $date->copy()->endOfDay())
            ->count();
    }
}
