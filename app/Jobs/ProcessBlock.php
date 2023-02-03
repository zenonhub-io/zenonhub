<?php

namespace App\Jobs;

use Log;
use App\Jobs\Alerts\WhaleAlert;
use App\Models\Nom\AccountBlock;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class ProcessBlock implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public int $backoff = 10;
    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block->refresh();
        $this->onQueue('indexer');
    }

    public function handle()
    {
        $jobBatch = [
            new ProcessAccountBalance($this->block->account),
            new ProcessAccountBalance($this->block->to_account),
            new WhaleAlert($this->block),
        ];

        if ($this->block->data && $this->block->contract_method) {
            $job = $this->getJobClass();
            array_unshift($jobBatch, new $job($this->block));
        }

        if (empty($jobBatch)) {
            return;
        }

        $block = $this->block;

        Bus::batch($jobBatch)
            ->then(function (Batch $batch) use ($block) {
                Log::debug('Block data processed ID: ' . $block->id);
            })->catch(function (Batch $batch, \Throwable $e) use ($block) {
                Log::error('Block data processing error ID: ' . $block->id);
            })
            ->onQueue('indexer')
            ->dispatch();
    }


    private function getJobClass(): ?string
    {
        $jobProcessors = [
            '1' => 'App\Jobs\Accelerator\ProjectCreated',
            '2' => 'App\Jobs\Accelerator\PhaseAdded',
            '3' => 'App\Jobs\Accelerator\PhaseUpdated',
            '4' => 'App\Jobs\Accelerator\Donate',
            '5' => 'App\Jobs\Accelerator\VoteByName',
            '6' => 'App\Jobs\Accelerator\VoteByProdAddress',
            '7' => 'App\Jobs\Common\DepositQsr',
            '8' => 'App\Jobs\Common\WithdrawQsr',
            '9' => 'App\Jobs\Common\CollectReward',
            '10' => 'App\Jobs\Pillars\Register',
            '11' => 'App\Jobs\Pillars\RegisterLegacy',
            '12' => 'App\Jobs\Pillars\Revoke',
            '13' => 'App\Jobs\Pillars\UpdatePillar',
            '14' => 'App\Jobs\Pillars\Delegate',
            '15' => 'App\Jobs\Pillars\Undelegate',
            '16' => 'App\Jobs\Plasma\Fuse',
            '17' => 'App\Jobs\Plasma\CancelFuse',
            '18' => 'App\Jobs\Sentinel\Register',
            '19' => 'App\Jobs\Sentinel\Revoke',
            '20' => 'App\Jobs\Stake\Stake',
            '21' => 'App\Jobs\Stake\Cancel',
            '22' => 'App\Jobs\Token\IssueToken',
            '23' => 'App\Jobs\Token\Mint',
            '24' => 'App\Jobs\Token\Burn',
            '25' => 'App\Jobs\Token\UpdateToken',
        ];

        return $jobProcessors[$this->block->contract_method?->id] ?? null;
    }
}
