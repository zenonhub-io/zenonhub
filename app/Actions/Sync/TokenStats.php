<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
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

class TokenStats
{
    use AsAction;

    public string $commandSignature = 'sync:token-stats';

    public function handle(Token $token, Carbon $date): void
    {
        $token->statHistory()->updateOrCreate([
            'date' => $date->format('Y-m-d'),
        ], [
            'daily_minted' => $this->getDailyMinted($token, $date),
            'daily_burned' => $this->getDailyBurned($token, $date),
            'total_supply' => $this->getTotalSupply($token, $date),
            'total_holders' => $this->getTotalHolders($token, $date),
            'total_transactions' => $this->getTotalTransactions($token, $date),
            'total_transferred' => $this->getTotalTransferred($token, $date),
            'total_locked' => $this->getTotalLocked($token, $date),
            'total_wrapped' => $this->getTotalWrapped($token, $date),
        ]);
    }

    public function asCommand(Command $command): void
    {
        $tokens = Token::whereIn('token_standard', [
            NetworkTokensEnum::ZNN->value,
            NetworkTokensEnum::QSR->value,
        ])->get();
        $period = CarbonPeriod::create(AccountBlock::min('created_at'), AccountBlock::max('created_at'));
        $progressBar = new ProgressBar(new ConsoleOutput, $period->count() * $tokens->count());
        $progressBar->start();

        foreach ($period as $date) {
            $tokens->each(function (Token $token) use ($progressBar, $date): void {
                $this->handle($token, $date);
                $progressBar->advance();
            });
        }

        $progressBar->finish();
    }

    private function getDailyMinted(Token $token, Carbon $date): string
    {
        $minted = $token->mints()
            ->whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->sum('amount');

        return number_format($minted, 0, '.', '');
    }

    private function getDailyBurned(Token $token, Carbon $date): string
    {
        $burns = $token->burns()
            ->whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->sum('amount');

        return number_format($burns, 0, '.', '');
    }

    private function getTotalSupply(Token $token, Carbon $date): string
    {
        $genesisSupply = 0;

        if ($token->token_standard === NetworkTokensEnum::ZNN->value) {
            $genesisSupply = Account::where('genesis_znn_balance', '>', '0')
                ->sum('genesis_znn_balance');
        }

        if ($token->token_standard === NetworkTokensEnum::QSR->value) {
            $genesisSupply = Account::where('genesis_qsr_balance', '>', '0')
                ->sum('genesis_qsr_balance');
        }

        $totalMints = $token->mints()
            ->where('created_at', '<=', $date->copy()->endOfDay())
            ->sum('amount');

        $totalBurns = $token->burns()
            ->where('created_at', '<=', $date->copy()->endOfDay())
            ->sum('amount');

        return number_format((($genesisSupply + $totalMints) - $totalBurns), 0, '.', '');
    }

    private function getTotalHolders(Token $token, Carbon $date): int
    {
        return $token->holders()->count();

        $accountCount = 0;

        $token->holders()
            ->whereNotEmbedded()
            ->chunk(200, function ($accounts) use ($token, $date, &$accountCount) {
                foreach ($accounts as $account) {
                    $sent = $account->sentBlocks()
                        ->selectRaw('CAST(SUM(amount) AS DECIMAL(65,0)) as total')
                        ->where('created_at', '<=', $date->copy()->endOfDay())
                        ->where('token_id', $token->id)
                        ->where('amount', '>', '0')
                        ->first()->total;

                    $received = $account->receivedBlocks()
                        ->selectRaw('CAST(SUM(amount) AS DECIMAL(65,0)) as total')
                        ->where('created_at', '<=', $date->copy()->endOfDay())
                        ->where('token_id', $token->id)
                        ->where('amount', '>', '0')
                        ->first()->total;

                    $genesisBalance = 0;

                    if ($token->token_standard === NetworkTokensEnum::ZNN->value) {
                        $genesisBalance = $account->genesis_znn_balance;
                    }

                    if ($token->token_standard === NetworkTokensEnum::QSR->value) {
                        $genesisBalance = $account->genesis_qsr_balance;
                    }

                    $totalBalance = ($genesisBalance + $received) - $sent;

                    if ($totalBalance > 0) {
                        $accountCount++;
                    }
                }
            });

        return $accountCount;
    }

    private function getTotalTransactions(Token $token, Carbon $date): string
    {
        return (string) $token->transactions()
            ->whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->count();
    }

    private function getTotalTransferred(Token $token, Carbon $date): string
    {
        $transferred = $token->transactions()
            ->whereBetween('created_at', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->sum('amount');

        return number_format($transferred, 0, '.', '');
    }

    private function getTotalLocked(Token $token, Carbon $date): string
    {
        if ($token->token_standard === NetworkTokensEnum::ZNN->value) {

            $totalPillars = Pillar::where('created_at', '<=', $date->copy()->endOfDay())
                ->where(function ($query) use ($date) {
                    $query->whereNull('revoked_at')
                        ->orWhere('revoked_at', '<=', $date->copy()->endOfDay());
                })->count();

            $totalSentinels = Sentinel::where('created_at', '<=', $date->copy()->endOfDay())
                ->where(function ($query) use ($date) {
                    $query->whereNull('revoked_at')
                        ->orWhere('revoked_at', '<=', $date->copy()->endOfDay());
                })->count();

            $totalStaked = Stake::where('token_id', $token->id)
                ->where('started_at', '<=', $date->copy()->endOfDay())
                ->where(function ($query) use ($date) {
                    $query->whereNull('ended_at')
                        ->orWhere('ended_at', '<=', $date->copy()->endOfDay());
                })->sum('amount');

            $totalZnnLocked = ($totalPillars * config('nom.pillar.znnStakeAmount'))
                + ($totalSentinels * config('nom.sentinel.znnRegisterAmount'))
                + $totalStaked;

            return number_format($totalZnnLocked, 0, '.', '');
        }

        if ($token->token_standard === NetworkTokensEnum::QSR->value) {
            $totalSentinels = Sentinel::where('created_at', '<=', $date->copy()->endOfDay())
                ->where(function ($query) use ($date) {
                    $query->whereNull('revoked_at')
                        ->orWhere('revoked_at', '<=', $date->copy()->endOfDay());
                })->count();

            $totalFused = Plasma::where('started_at', '<=', $date->copy()->endOfDay())
                ->where(function ($query) use ($date) {
                    $query->whereNull('ended_at')
                        ->orWhere('ended_at', '<=', $date->copy()->endOfDay());
                })->sum('amount');

            $totalQsrLocked = ($totalSentinels * config('nom.sentinel.qsrDepositAmount'))
                + $totalFused;

            return number_format($totalQsrLocked, 0, '.', '');
        }

        return '0';
    }

    private function getTotalWrapped(Token $token, Carbon $date): string
    {
        return '0';
    }
}
