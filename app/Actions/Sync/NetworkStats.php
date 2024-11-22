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
        return AccountBlock::whereDate('created_at', $operator, $date)->count();
    }

    /**
     * Get the total number of addresses in the network up to the given date
     */
    private function getTotalAddresses(Carbon $date): int
    {
        return Account::whereDate('first_active_at', '<=', $date)->count();
    }

    /**
     * Get the total number of addresses registered on the given date
     */
    private function getTotalDailyAddresses(Carbon $date): int
    {
        return Account::whereDate('first_active_at', $date)->count();
    }

    /**
     * Get the total number of addresses active on the given date
     */
    private function getTotalActiveAddresses(Carbon $date): int
    {
        return Account::whereHas('sentBlocks', function ($query) use ($date) {
            $query->whereDate('created_at', $date);
        })->count();
    }

    private function getTotalTokens(Carbon $date, string $operator = '='): int
    {
        return Token::whereDate('created_at', $operator, $date)->count();
    }

    private function getTotalStakes(Carbon $date, string $operator = '='): int
    {
        $znnToken = app('znnToken');

        return Stake::where('token_id', $znnToken->id)
            ->whereActive()
            ->whereDate('started_at', $operator, $date)
            ->count();
    }

    private function getTotalStaked(Carbon $date, string $operator = '='): mixed
    {
        $znnToken = app('znnToken');

        return Stake::where('token_id', $znnToken->id)
            ->whereActive()
            ->whereDate('started_at', $operator, $date)
            ->sum('amount');
    }

    private function getTotalFusions(Carbon $date, string $operator = '='): int
    {
        return Plasma::whereActive()
            ->whereDate('started_at', $operator, $date)
            ->count();
    }

    private function getTotalFused(Carbon $date, string $operator = '='): mixed
    {
        return Plasma::whereActive()
            ->whereDate('started_at', $operator, $date)
            ->sum('amount');
    }

    private function getTotalPillars(Carbon $date): int
    {
        return Pillar::whereActive()->whereDate('created_at', '<=', $date)->count();
    }

    private function getTotalSentinels(Carbon $date): int
    {
        return Sentinel::whereActive()->whereDate('created_at', '<=', $date)->count();
    }
}
