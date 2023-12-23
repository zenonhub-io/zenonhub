<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeUnwrap;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RevokeUnwrapRequest implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public BridgeUnwrap $unwrap;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        try {
            $this->loadUnwrap();
            $this->processRevokeUnwrap();
        } catch (\Throwable $exception) {
            Log::warning('Error revoking unwrap request '.$this->block->hash);
            Log::debug($exception);

            return;
        }

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function loadUnwrap(): void
    {
        $data = $this->block->data->decoded;
        $this->unwrap = BridgeUnwrap::where('transaction_hash', $data['transactionHash'])
            ->where('log_index', $data['logIndex'])
            ->sole();
    }

    private function processRevokeUnwrap(): void
    {
        $this->unwrap->revoked_at = $this->block->created_at;
        $this->unwrap->save();
    }
}
