<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Accelerator\PillarVoted;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\Vote;
use Illuminate\Support\Facades\Log;

class VoteByProdAddress extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::firstWhere('producer_account_id', $accountBlock->account_id);
        $item = AcceleratorProject::firstWhere('hash', $blockData['id']);
        if (! $item) {
            $item = AcceleratorPhase::firstWhere('hash', $blockData['id']);
        }

        try {
            $this->validateAction($accountBlock, $pillar, $item);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Accelerator: VoteByProdAddress failed', [
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
            'is_yes' => Vote::isVoteType('yes', $blockData['vote']),
            'is_no' => Vote::isVoteType('no', $blockData['vote']),
            'is_abstain' => Vote::isVoteType('abstain', $blockData['vote']),
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

        Log::info('Contract Method Processor - Accelerator: VoteByProdAddress complete', [
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
    }
}