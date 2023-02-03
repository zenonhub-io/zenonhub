<?php

namespace App\Jobs\Accelerator;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AccountBlock;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PhaseUpdated implements ShouldQueue
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
        $blockData = $this->block->data->decoded;

        $phase = AcceleratorPhase::where('hash', $blockData['id'])->first();

        if ($phase) {
            $phase->name = $blockData['name'];
            $phase->description = $blockData['description'];
            $phase->url = $blockData['url'];
            $phase->znn_funds_needed = $blockData['znnFundsNeeded'];
            $phase->qsr_funds_needed = $blockData['qsrFundsNeeded'];
            $phase->updated_at = $this->block->momentum->created_at;
            $phase->save();

            $phase->project->modified_at = $this->block->momentum->created_at;
            $phase->project->save();
        }
    }
}
