<?php

namespace App\Jobs\Token;

use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateToken implements ShouldQueue
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

        $token = Token::whereZts($blockData['tokenStandard'])->first();

        if ($token) {
            $token->is_burnable = $blockData['isBurnable'];
            $token->is_mintable = $blockData['isMintable'];
            $token->updated_at = $this->block->created_at;
            $token->save();
        }
    }
}
