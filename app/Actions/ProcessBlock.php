<?php

namespace App\Actions;

use App\Jobs\Alerts\WhaleAlert;
use App\Jobs\ProcessAccountBalance;
use App\Models\Nom\AccountBlock;
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
        if ($this->block->data && $this->block->contract_method) {
            $jobClassName = $this->block->contract_method?->job_class_name;
            if ($jobClassName && class_exists($jobClassName)) {
                $jobClassName::dispatch($this->block);
            }
        }

        if ($this->fireWhaleAlerts && $this->block->amount > 0 && in_array($this->block->token_id, [1, 2])) {
            WhaleAlert::dispatch($this->block);
        }

        if ($this->processAccounts) {
            if ($this->block->account->address !== Account::ADDRESS_EMPTY) {
                ProcessAccountBalance::dispatch($this->block->account)->delay(now()->addSeconds(30));
            }

            if ($this->block->to_account->address !== Account::ADDRESS_EMPTY) {
                ProcessAccountBalance::dispatch($this->block->to_account)->delay(now()->addSeconds(30));
            }
        }
    }
}
