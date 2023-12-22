<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeWrap;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateWrapRequest implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public array $blockData;

    public BridgeWrap $wrap;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->blockData = $this->block->data->decoded;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        try {
            $this->loadWrap();
            $this->processUpdate();
        } catch (\Throwable $exception) {
            Log::error('Error updating wrap request '.$this->block->hash);
            Log::error($exception->getMessage());

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function loadWrap(): void
    {
        $this->wrap = BridgeWrap::whereHas('account_block', fn ($q) => $q->where('hash', $this->blockData['id']))
            ->sole();
    }

    private function processUpdate(): void
    {
        $this->wrap->signature = $this->blockData['signature'];
        $this->wrap->updated_at = $this->block->created_at;
        $this->wrap->save();
    }
}
