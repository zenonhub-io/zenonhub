<?php

namespace App\Jobs\Nom\Accelerator;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AccountBlock;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdatePhase implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $this->updatePhase();
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function updatePhase()
    {
        $blockData = $this->block->data->decoded;

        $phase = AcceleratorPhase::where('hash', $blockData['id'])->first();

        if ($phase) {
            $phase->name = $blockData['name'];
            $phase->description = $blockData['description'];
            $phase->url = $blockData['url'];
            $phase->znn_requested = $blockData['znnFundsNeeded'];
            $phase->qsr_requested = $blockData['qsrFundsNeeded'];
            $phase->updated_at = $this->block->momentum->created_at;
            $phase->save();

            $phase->project->modified_at = $this->block->momentum->created_at;
            $phase->project->save();
        }
    }
}
