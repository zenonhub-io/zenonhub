<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Accelerator;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Accelerator\PillarVoted;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Models\Nom\Vote;
use Illuminate\Support\Facades\Log;

class VoteByName extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::firstWhere('name', $blockData['name']);
        $item = AcceleratorProject::firstWhere('hash', $blockData['id']);
        if (! $item) {
            $item = AcceleratorPhase::firstWhere('hash', $blockData['id']);
        }

        try {
            $this->validateAction($accountBlock, $pillar, $item);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Accelerator: VoteByName failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        Vote::updateOrCreate([
            'owner_id' => $pillar->owner_id,
            'pillar_id' => $pillar->id,
            'votable_id' => $item->id,
            'votable_type' => $item::class,
        ], [
            'vote' => $blockData['vote'],
            'created_at' => $accountBlock->created_at,
        ]);

        $item->total_votes = $item->votes()->count();
        $item->total_yes_votes = $item->votes()->whereYesVote()->count();
        $item->total_no_votes = $item->votes()->whereNoVote()->count();
        $item->total_abstain_votes = $item->votes()->whereAbstainVote()->count();
        $item->updated_at = $accountBlock->created_at;
        $item->save();

        //$pillar->updateAzEngagementScores();

        PillarVoted::dispatch($accountBlock, $pillar, $item);

        Log::info('Contract Method Processor - Accelerator: VoteByName complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Pillar $pillar
         */
        [$accountBlock, $pillar, $item] = func_get_args();

        if (! $pillar) {
            throw new IndexerActionValidationException('Invalid pillar');
        }

        if (! $item) {
            throw new IndexerActionValidationException('Invalid votable item');
        }

        if ($pillar->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Account is not pillar owner');
        }
    }
}
