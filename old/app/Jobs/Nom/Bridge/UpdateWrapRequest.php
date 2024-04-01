<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeWrap;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        } catch (Throwable $exception) {
            Log::warning('Error updating wrap request ' . $this->block->hash);
            Log::debug($exception);

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function loadWrap(): void
    {
        $this->wrap = BridgeWrap::whereRelation('accountBlock', 'hash', $this->blockData['id'])
            ->sole();
    }

    private function processUpdate(): void
    {
        $this->wrap->signature = $this->blockData['signature'];
        $this->wrap->updated_at = $this->block->created_at;
        $this->wrap->save();
    }
}
