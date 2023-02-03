<?php

namespace App\Classes;

use Cache;
use DB;
use App\Jobs\ProcessBlock;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountBlockData;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Momentum;
use App\Models\Nom\Pillar;
use App\Models\Nom\PillarHistory;
use App\Models\Nom\Token;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Collection;

class Indexer
{
    protected Zenon $znn;
    protected int $totalMomentums;
    protected Collection $momentums;
    protected ?OutputStyle $console;

    public function __construct(Zenon $znn, ?OutputStyle $console = null)
    {
        $this->znn = $znn;
        $this->console = $console;
    }

    public function run(): void
    {
        $this->createFirstMomentum();
        $this->createFirstBlock();
        $this->processMomentums();
    }

    private function loadMomentums(): self
    {
        $momentumCount = Momentum::max('height');

        // Re-sync last momentum to be safe
        if ($momentumCount > 0) {
            $momentumCount--;
        }

        // If only dummy momentum sync height from 1
        if ($momentumCount === 0) {
            $momentumCount++;
        }

        $momentums = $this->znn->ledger->getMomentumsByHeight($momentumCount);
        $this->momentums = collect($momentums['data']->list);
        $this->totalMomentums = $momentums['data']->count;

        return $this;
    }

    private function createFirstMomentum(): self
    {
        if (! Momentum::count()) {
            Momentum::create([
                'version' => 1,
                'chain_identifier' => 1,
                'height' => 0,
                'hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                'public_key' => null,
                'signature' => null,
                'data' => null,
                'created_at' => '2021-11-24 12:00:00',
            ]);
        }

        return $this;
    }

    private function createFirstBlock(): self
    {
        if (! AccountBlock::count()) {
            $account = Utilities::loadAccount('z1qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqsggv2f');
            AccountBlock::create([
                'account_id' => $account->id,
                'to_account_id' => $account->id,
                'momentum_id' => 1,
                'parent_block_id' => null,
                'paired_account_block_id' => null,
                'token_id' => null,
                'version' => 0,
                'chain_identifier' => 0,
                'block_type' => 4,
                'height' => 0,
                'amount' => 0,
                'fused_plasma' => 0,
                'base_plasma' => 0,
                'used_plasma' => 0,
                'difficulty' => 0,
                'hash' => '0000000000000000000000000000000000000000000000000000000000000000',
                'nonce' => '0000000000000000',
                'public_key' => null,
                'signature' => null,
                'created_at' => '2021-11-24 12:00:00',
            ]);
        }

        return $this;
    }

    private function processMomentums(): self
    {
        $this->loadMomentums();

        $this->momentums->each(function ($data) {

            $momentum = Momentum::where('hash', $data->hash)->first();
            $producer = Utilities::loadAccount($data->producer);
            $pillar = Pillar::where('producer_id', $producer?->id)->first();

            if (!$pillar) {
                $history = PillarHistory::where('producer_id', $producer?->id)->first();
                if ($history) {
                    $pillar = $history->pillar;
                }
            }

            DB::beginTransaction();

            try {

                if (!$momentum) {
                    Momentum::create([
                        'producer_account_id' => $producer?->id,
                        'producer_pillar_id' => $pillar?->id,
                        'version' => $data->version,
                        'chain_identifier' => $data->chainIdentifier,
                        'height' => $data->height,
                        'hash' => $data->hash,
                        'public_key' => $data->publicKey,
                        'signature' => $data->signature,
                        'data' => $data->data,
                        'created_at' => $data->timestamp,
                    ]);
                }

                $blocks = collect($data->content);
                $blocks->each(function ($data) {
                    $this->processBlock($data->hash);
                });

                Cache::put('momentum-count', Momentum::max('height'));

                DB::commit();
            } catch (\Exception $e) {
                \Log::info('Processing momentum failed', (array) $data);
                \Log::error($e);
                DB::rollBack();
                exit;
            }
        });

        return $this;
    }

    private function processBlock($hash): ?AccountBlock
    {
        if (! $hash) {
            return null;
        }

        $data = $this->znn->ledger->getAccountBlockByHash($hash)['data'];

        if ($data) {

            $block = AccountBlock::where('hash', $data->hash)->first();
            $momentum = Momentum::where('hash', $data->confirmationDetail?->momentumHash)->first();
            $momentumAcknowledged = Momentum::where('hash', $data->momentumAcknowledged?->hash)->first();

            if (! $block) {

                $account = Utilities::loadAccount($data->address);
                $toAccount = Utilities::loadAccount($data->toAddress);
                $token = Token::whereZts($data->token?->tokenStandard)->first();

                if (! $account->public_key) {
                    $account->public_key = $data->publicKey;
                    $account->save();
                }

                $block = AccountBlock::create([
                    'account_id' => $account?->id,
                    'to_account_id' => $toAccount?->id,
                    'momentum_id' => $momentum?->id,
                    'momentum_acknowledged_id' => $momentumAcknowledged?->id,
                    'token_id' => $token?->id,
                    'version' => $data->version,
                    'chain_identifier' => $data->chainIdentifier,
                    'block_type' => $data->blockType,
                    'height' => $data->height,
                    'amount' => $data->amount,
                    'fused_plasma' => $data->fusedPlasma,
                    'base_plasma' => $data->basePlasma,
                    'used_plasma' => $data->usedPlasma,
                    'difficulty' => $data->difficulty,
                    'hash' => $data->hash,
                    'nonce' => $data->nonce,
                    'public_key' => $data->publicKey,
                    'signature' => $data->signature,
                    'created_at' => $data->confirmationDetail?->momentumTimestamp,
                ]);

                if ($data->pairedAccountBlock) {
                    $pairedAccountBlock = $this->processBlock($data->pairedAccountBlock->hash);
                    if ($pairedAccountBlock) {
                        $block->paired_account_block_id = $pairedAccountBlock?->id;
                        $block->save();

                        $pairedAccountBlock->paired_account_block_id = $block->id;
                        $pairedAccountBlock->save();
                    }
                }

                if (! empty($data->descendantBlocks)) {
                    foreach ($data->descendantBlocks as $descendant) {
                        $descendantBlock = $this->processBlock($descendant->hash);
                        $descendantBlock->parent_block_id = $block->id;
                        $descendantBlock->save();
                    }
                }

                if ($token && $data->amount > 0) {
                    if ($token->token_standard === Token::ZTS_ZNN) {
                        $account->total_znn_sent += $data->amount;
                        $toAccount->total_znn_received += $data->amount;

                        $account->save();
                        $toAccount->save();
                    }

                    if ($token->token_standard === Token::ZTS_QSR) {
                        $account->total_qsr_sent += $data->amount;
                        $toAccount->total_qsr_received += $data->amount;

                        $account->save();
                        $toAccount->save();
                    }
                }

                Cache::put('transaction-count', (AccountBlock::count() - 1));
                Cache::put('address-count', Account::count());

                $this->saveBlockData($data->data, $block);

                ProcessBlock::dispatch($block)->delay(now()->addSeconds(15));

            } else {
                $block->momentum_id = ($momentum ? $momentum->id : $block->momentum_id);
                $block->momentum_acknowledged_id = $momentumAcknowledged?->id;
                $block->height = $data->height;
                $block->save();
            }

            return $block;
        }

        return null;
    }

    private function saveBlockData($data, $block) :?AccountBlockData
    {
        if (empty($data)) {
            return null;
        }

        $fingerprint = ZnnUtilities::getDataFingerprint($data);
        $contractMethod = ContractMethod::findByFingerprint($fingerprint);

        if ($contractMethod) {
            $contract = "DigitalSloth\ZnnPhp\Abi\\" . $contractMethod->contract->name;
            $contract = new $contract();

            return AccountBlockData::create([
                'account_block_id' => $block->id,
                'contract_method_id' => $contractMethod->id,
                'raw' => $data,
                'decoded' => $contract->decode($contractMethod->name, $data),
            ]);
        } else {
            return AccountBlockData::create([
                'account_block_id' => $block->id,
                'raw' => $data,
            ]);
        }
    }
}
