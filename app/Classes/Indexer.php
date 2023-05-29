<?php

namespace App\Classes;

use App\Events\Nom\AccountBlockProcessed;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountBlockData;
use App\Models\Nom\AccountReward;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Momentum;
use App\Models\Nom\Pillar;
use App\Models\Nom\PillarHistory;
use App\Models\Nom\Token;
use Cache;
use DB;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Support\Collection;
use Log;

class Indexer
{
    protected int $totalMomentums;

    protected Collection $momentums;

    public function __construct(
        protected Zenon $znn,
        protected bool $auto,
        protected ?int $startHeight,
        protected ?bool $sendWhaleAlerts,
        protected ?bool $syncAccountBalances,
    ) {
    }

    public function run(): void
    {
        if ($this->auto && ! config('explorer.enable_indexer')) {
            return;
        }

        $momentum = $this->znn->ledger->getFrontierMomentum()['data'];

        while ($momentum->height > ($count = Momentum::max('height'))) {
            try {
                $this->loadMomentums($count);
                $this->processMomentums();
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
    }

    private function loadMomentums($count): void
    {
        if ($this->startHeight) {
            $height = $this->startHeight;
            $this->startHeight = null;
        } else {
            $height = $count;
        }

        $momentums = $this->znn->ledger->getMomentumsByHeight($height);
        $this->momentums = collect($momentums['data']->list);
        $this->totalMomentums = $momentums['data']->count;
    }

    private function processMomentums(): void
    {
        $this->momentums->each(function ($data) {
            $chain = Utilities::loadChain();
            $momentum = Momentum::where('hash', $data->hash)->first();
            $producer = Utilities::loadAccount($data->producer);
            $pillar = Pillar::where('producer_id', $producer?->id)->first();

            if (! $pillar) {
                $history = PillarHistory::where('producer_id', $producer?->id)->first();
                if ($history) {
                    $pillar = $history->pillar;
                }
            }

            if (! $momentum) {
                Momentum::create([
                    'chain_id' => $chain->id,
                    'producer_account_id' => $producer?->id,
                    'producer_pillar_id' => $pillar?->id,
                    'version' => $data->version,
                    'height' => $data->height,
                    'hash' => $data->hash,
                    'data' => $data->data,
                    'created_at' => $data->timestamp,
                ]);
            }

            $blocks = collect($data->content);
            $blocks->each(function ($data) {
                DB::beginTransaction();
                try {
                    $this->processBlock($data->hash);
                    DB::commit();
                } catch (\Exception $exception) {
                    Log::error('Could not process block '.$data->hash);
                    Log::error($exception);
                    DB::rollBack();
                    exit;
                }
            });
        });

        Cache::put('momentum-count', Momentum::count());
        Cache::put('transaction-count', AccountBlock::count());
        Cache::put('address-count', Account::count());
    }

    private function processBlock(string $hash): ?AccountBlock
    {
        if (! $hash) {
            return null;
        }

        $data = $this->znn->ledger->getAccountBlockByHash($hash)['data'];

        if (! $data) {
            return null;
        }

        $block = AccountBlock::where('hash', $data->hash)->first();
        $momentum = Momentum::where('hash', $data->confirmationDetail?->momentumHash)->first();
        $momentumAcknowledged = Momentum::where('hash', $data->momentumAcknowledged?->hash)->first();

        if (! $block) {
            $chain = Utilities::loadChain();
            $account = Utilities::loadAccount($data->address);
            $toAccount = Utilities::loadAccount($data->toAddress);
            $token = Utilities::loadToken($data->token?->tokenStandard);

            if (! $account->public_key) {
                $account->public_key = $data->publicKey;
                $account->save();
            }

            if (! $account->first_active_at) {
                $account->first_active_at = $data->confirmationDetail?->momentumTimestamp;
                $account->save();
            }

            $block = AccountBlock::create([
                'chain_id' => $chain->id,
                'account_id' => $account?->id,
                'to_account_id' => $toAccount?->id,
                'momentum_id' => $momentum?->id,
                'momentum_acknowledged_id' => $momentumAcknowledged?->id,
                'token_id' => $token?->id,
                'version' => $data->version,
                'block_type' => $data->blockType,
                'height' => $data->height,
                'amount' => $data->amount,
                'fused_plasma' => $data->fusedPlasma,
                'base_plasma' => $data->basePlasma,
                'used_plasma' => $data->usedPlasma,
                'difficulty' => $data->difficulty,
                'hash' => $data->hash,
                'created_at' => $data->confirmationDetail?->momentumTimestamp,
            ]);

            if (! empty($data->descendantBlocks)) {
                $this->createDescendantBlocks($block, $data->descendantBlocks);
            }

            if ($data->pairedAccountBlock) {
                $this->createPairedAccountBlock($block, $data->pairedAccountBlock);
            }

            if ($token && $data->amount > 0) {
                $this->updateTokenTransferTotals($account, $toAccount, $token, $data);
            }

            if ($block->token?->id === 2 && $data->address === Account::ADDRESS_LIQUIDITY_PROGRAM_DISTRIBUTOR) {
                $this->processLiquidityProgramRewards($block);
            }

            if (! empty($data->data)) {
                $this->createBlockData($block, $data);
            }

            AccountBlockProcessed::dispatch($block);

            // If not to empty address and not from pillar producer address
            if ($block->to_account->address !== Account::ADDRESS_EMPTY) {
                (new \App\Actions\ProcessBlock(
                    $block,
                    $this->sendWhaleAlerts,
                    $this->syncAccountBalances
                ))->execute();
            }
        } else {
            $block->momentum_id = ($momentum ? $momentum->id : $block->momentum_id);
            $block->momentum_acknowledged_id = $momentumAcknowledged?->id;
            $block->height = $data->height;
            $block->save();
        }

        return $block;
    }

    private function createDescendantBlocks(AccountBlock $block, array $data): void
    {
        foreach ($data as $descendant) {
            $descendantBlock = $this->processBlock($descendant->hash);
            $descendantBlock->parent_block_id = $block->id;
            $descendantBlock->save();
        }
    }

    private function createPairedAccountBlock(AccountBlock $block, object $data): void
    {
        $pairedAccountBlock = $this->processBlock($data->hash);
        if ($pairedAccountBlock) {
            $block->paired_account_block_id = $pairedAccountBlock?->id;
            $block->save();

            $pairedAccountBlock->paired_account_block_id = $block->id;
            $pairedAccountBlock->save();
        }
    }

    private function updateTokenTransferTotals(Account $account, Account $toAccount, Token $token, object $data): void
    {
        $save = false;

        if ($token->token_standard === Token::ZTS_ZNN) {
            $account->total_znn_sent += $data->amount;
            $toAccount->total_znn_received += $data->amount;
            $save = true;
        }

        if ($token->token_standard === Token::ZTS_QSR) {
            $account->total_qsr_sent += $data->amount;
            $toAccount->total_qsr_received += $data->amount;
            $save = true;
        }

        if ($save) {
            $account->save();
            $toAccount->save();
        }
    }

    private function processLiquidityProgramRewards(AccountBlock $block): void
    {
        AccountReward::create([
            'chain_id' => $block->chain->id,
            'account_id' => $block->to_account->id,
            'token_id' => $block->token->id,
            'type' => AccountReward::TYPE_LIQUIDITY_PROGRAM,
            'amount' => $block->amount,
            'created_at' => $block->created_at,
        ]);
    }

    private function createBlockData(AccountBlock $block, mixed $rawBlock): void
    {
        $data = base64_decode($rawBlock->data);
        $decodedData = null;

        // Index embedded contracts
        if (
            in_array($block->block_type, [AccountBlock::TYPE_SEND, AccountBlock::TYPE_CONTRACT_SEND])
            && ! in_array($block->account->id, Account::getAllPillarProducerAddresses()->toArray())
        ) {

            $contract = $block->to_account->contract;
            $fingerprint = ZnnUtilities::getDataFingerprint($data);
            $contractMethod = ContractMethod::where('contract_id', $contract?->id)
                ->where('fingerprint', $fingerprint)
                ->first();

            // Fallback for common methods (not related to a specific account)
            if (! $contractMethod) {
                $contractMethod = ContractMethod::whereHas('contract', fn ($q) => $q->where('name', 'Common'))
                    ->where('fingerprint', $fingerprint)
                    ->first();
            }

            if ($contractMethod) {
                $block->contract_method_id = $contractMethod->id;
                $block->save();

                $contractName = ucfirst(strtolower($contractMethod->contract->name));
                $embeddedContract = "DigitalSloth\ZnnPhp\Abi\Contracts\\".$contractName;

                if (class_exists($embeddedContract)) {
                    $embeddedContract = new $embeddedContract();
                    $decoded = $embeddedContract->decode($contractMethod->name, $data);
                    $parameters = $embeddedContract->getParameterNames($contractMethod->name);

                    if ($decoded && $parameters) {
                        $parameters = explode(',', $parameters);

                        $decodedData = array_combine(
                            $parameters,
                            $decoded
                        );
                    }
                }
            }
        }

        AccountBlockData::create([
            'account_block_id' => $block->id,
            'raw' => base64_encode($data),
            'decoded' => $decodedData,
        ]);
    }
}
