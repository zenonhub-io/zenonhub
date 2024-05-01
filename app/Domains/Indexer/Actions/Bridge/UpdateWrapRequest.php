<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeWrap;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateWrapRequest extends AbstractIndexerAction
{
    public array $blockData;

    public BridgeWrap $wrap;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->blockData = $this->accountBlock->data->decoded;
        $this->onQueue('indexer');
    }

    public function handle(AccountBlock $accountBlock): void
    {
        try {
            $this->loadWrap();
            $this->processUpdate();
        } catch (Throwable $exception) {
            Log::warning('Error updating wrap request ' . $this->accountBlock->hash);
            Log::debug($exception);

            return;
        }

    }

    private function loadWrap(): void
    {
        $this->wrap = BridgeWrap::whereRelation('accountBlock', 'hash', $this->blockData['id'])
            ->sole();
    }

    private function processUpdate(): void
    {
        $this->wrap->signature = $this->blockData['signature'];
        $this->wrap->updated_at = $this->accountBlock->created_at;
        $this->wrap->save();
    }
}
