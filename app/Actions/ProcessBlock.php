<?php

namespace App\Actions;

use App\Jobs\Alerts\WhaleAlert;
use App\Jobs\ProcessAccountBalance;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use Log;
use Spatie\QueueableAction\QueueableAction;

class ProcessBlock
{
    use QueueableAction;

    public function __construct(
        protected AccountBlock $block,
        protected $fireWhaleAlerts = false,
        protected $processAccounts = false
    ) {
        $this->block->refresh();
    }

    public function execute(): void
    {
        Log::debug('Processing block '.$this->block->hash);
        Log::debug('Whale alerts '.($this->fireWhaleAlerts ? 'Yes' : 'No'));
        Log::debug('Account balances '.($this->processAccounts ? 'Yes' : 'No'));

        if ($this->block->data && $this->block->contract_method) {
            $jobClassName = $this->block->contract_method?->job_class_name;
            if ($jobClassName && class_exists($jobClassName)) {
                $jobClassName::dispatch($this->block);
            }
        }

        if ($this->fireWhaleAlerts && $this->block->amount > 0 && in_array($this->block->token_id, [1, 2])) {
            Log::debug('Fire whale alerts');
            WhaleAlert::dispatch($this->block);
        }

        if ($this->processAccounts) {

            $delay = now()->addSeconds(30);

            if ($this->block->account->address !== Account::ADDRESS_EMPTY) {
                Log::debug('Dispatch account balances job '.$this->block->account->address);
                ProcessAccountBalance::dispatch($this->block->account)->delay($delay);
            }

            if ($this->block->to_account->address !== Account::ADDRESS_EMPTY) {
                Log::debug('Dispatch account balances job '.$this->block->to_account->address);
                ProcessAccountBalance::dispatch($this->block->to_account)->delay($delay);
            }
        }

        Log::debug('End processing block');
    }
}
