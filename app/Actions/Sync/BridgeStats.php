<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeNetwork;
use App\Models\Nom\BridgeStatHistory;
use App\Models\Nom\BridgeUnwrap;
use App\Models\Nom\BridgeWrap;
use App\Models\Nom\Token;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class BridgeStats
{
    use AsAction;

    public string $commandSignature = 'sync:bridge-stats';

    public function handle(Carbon $date): void
    {
        $networks = BridgeNetwork::get();
        $tokens = Token::whereIn('token_standard', [
            NetworkTokensEnum::ZNN->value,
            NetworkTokensEnum::QSR->value,
        ])->get();

        $tokens->each(function (Token $token) use ($date, $networks): void {
            $networks->each(function (BridgeNetwork $bridgeNetwork) use ($token, $date) {
                BridgeStatHistory::updateOrCreate([
                    'date' => $date->format('Y-m-d'),
                    'bridge_network_id' => $bridgeNetwork->id,
                    'token_id' => $token->id,
                ], [
                    'wrap_tx' => $this->getWrapTx($bridgeNetwork, $token, $date),
                    'wrapped_amount' => $this->getWrappedAmount($bridgeNetwork, $token, $date),
                    'unwrap_tx' => $this->getUnWrapTx($bridgeNetwork, $token, $date),
                    'unwrapped_amount' => $this->getUnWrappedAmount($bridgeNetwork, $token, $date),
                    'affiliate_tx' => $this->getAffiliateTx($bridgeNetwork, $token, $date),
                    'affiliate_amount' => $this->getAffiliateAmount($bridgeNetwork, $token, $date),
                    'total_volume' => $this->getTotalVolume($bridgeNetwork, $token, $date),
                    'total_flow' => $this->getTotalFlow($bridgeNetwork, $token, $date),
                ]);
            });
        });
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

    private function getWrapTx(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $totalTx = BridgeWrap::where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->count();

        return number_format($totalTx, 0, '.', '');
    }

    private function getWrappedAmount(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $amount = BridgeWrap::where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        return number_format($amount, 0, '.', '');
    }

    private function getUnWrapTx(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $totalTx = BridgeUnwrap::whereNotAffiliateReward()
            ->where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->count();

        return number_format($totalTx, 0, '.', '');
    }

    private function getUnWrappedAmount(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $amount = BridgeUnwrap::whereNotAffiliateReward()
            ->where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        return number_format($amount, 0, '.', '');
    }

    private function getAffiliateTx(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $totalTx = BridgeUnwrap::whereAffiliateReward()
            ->where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->count();

        return number_format($totalTx, 0, '.', '');
    }

    private function getAffiliateAmount(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $amount = BridgeUnwrap::whereAffiliateReward()
            ->where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        return number_format($amount, 0, '.', '');
    }

    private function getTotalVolume(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $totalOutbound = BridgeWrap::where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        $totalInbound = BridgeUnwrap::where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        return number_format($totalOutbound + $totalInbound, 0, '.', '');
    }

    private function getTotalFlow(BridgeNetwork $network, Token $token, Carbon $date): string
    {
        $totalOutbound = BridgeWrap::where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        $totalInbound = BridgeUnwrap::where('bridge_network_id', $network->id)
            ->where('token_id', $token->id)
            ->whereDate('created_at', $date)
            ->sum('amount');

        return number_format($totalInbound - $totalOutbound, 0, '.', '');
    }
}
