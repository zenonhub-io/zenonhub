<?php

namespace App\Actions;

use App\Bots\BridgeAlertBot;
use App\Bots\WhaleAlertBot;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use App\Notifications\Bots\BridgeAlert;
use App\Notifications\Bots\WhaleAlert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\QueueableAction\QueueableAction;

class ProcessBlock
{
    use QueueableAction;

    public function __construct(
        protected AccountBlock $block,
        protected bool $fireAlerts = false
    ) {
        $this->block->refresh();
    }

    public function execute(): void
    {
        Log::debug('Processing block '.$this->block->hash, [
            'alerts' => ($this->fireAlerts ? 'Yes' : 'No'),
        ]);

        if ($this->block->data && $this->block->contract_method) {
            $jobClassName = $this->block->contract_method?->job_class_name;
            if ($jobClassName && class_exists($jobClassName)) {
                $jobClassName::dispatch($this->block);
            }
        }

        $jobDelay = now()->addSeconds(30);

        if ($this->fireAlerts && $this->shouldSendWhaleAlerts()) {
            Log::debug('Fire whale alert');
            Notification::send(new WhaleAlertBot, (new WhaleAlert($this->block))->delay($jobDelay));
        }

        if ($this->fireAlerts && $this->shouldSendBridgeAlerts()) {
            Log::debug('Fire bridge alert');
            Notification::send(new BridgeAlertBot(), (new BridgeAlert($this->block))->delay($jobDelay));
        }
    }

    private function shouldSendWhaleAlerts(): bool
    {
        $znnValue = config('bots.whale-alerts.znn_cutoff') * 100000000;
        $qsrValue = config('bots.whale-alerts.qsr_cutoff') * 100000000;

        if (! $this->block->token) {
            return false;
        }

        if ($this->block->token->token_standard === Token::ZTS_ZNN && $this->block->amount >= $znnValue) {
            return true;
        }

        if ($this->block->token->token_standard === Token::ZTS_QSR && $this->block->amount >= $qsrValue) {
            return true;
        }

        return false;
    }

    private function shouldSendBridgeAlerts(): bool
    {
        $watchAddresses = config('bots.bridge-alerts.watch_addresses');
        $watchMethods = config('bots.bridge-alerts.watch_methods');

        if (! in_array($this->block->to_account->address, [Account::ADDRESS_BRIDGE, Account::ADDRESS_LIQUIDITY])) {
            return false;
        }

        if (! in_array($this->block->account->address, $watchAddresses)) {
            return false;
        }

        if (! in_array($this->block->contract_method->name, $watchMethods)) {
            return false;
        }

        return true;
    }
}
