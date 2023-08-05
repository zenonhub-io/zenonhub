<?php

use App\Jobs\ProcessAccountBalance;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Token;
use App\Models\Nom\TokenBurn;
use App\Models\Nom\TokenMint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nom_tokens', function (Blueprint $table) {
            $table->string('total_supply')->default(0)->change();
            $table->string('max_supply')->default(0)->change();
        });

        Schema::table('nom_account_blocks', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_account_tokens', function (Blueprint $table) {
            $table->string('balance')->default(0)->change();
        });

        Schema::table('nom_account_rewards', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_token_burns', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_token_mints', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_fusions', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_stakes', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        if (! app()->environment('testing')) {
            $this->updateTokens();
            $this->updateAccountBlocks();
            $this->updateAccountBalances();
            $this->updateMints();
            $this->updateBurns();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

    private function updateTokens(): void
    {
        $token = Token::findByZts(Token::ZTS_LP_ETH);
        $token->total_supply = $token->raw_json->totalSupply;
        $token->max_supply = $token->raw_json->maxSupply;
        $token->save();
    }

    private function updateAccountBlocks(): void
    {
        $znn = App::make('zenon.api');
        $token = Token::findByZts(Token::ZTS_LP_ETH);
        $blocks = AccountBlock::where('token_id', $token->id)->where('amount', '>', 0)->get();
        $blocks->each(function ($block) use ($znn) {
            $data = $znn->ledger->getAccountBlockByHash($block->hash)['data'];
            $block->amount = $data->amount;
            $block->save();
        });
    }

    private function updateAccountBalances(): void
    {
        $token = Token::findByZts(Token::ZTS_LP_ETH);
        $accounts = Account::whereHas('balances', fn ($q) => $q->where('token_id', $token->id))->get();
        $accounts->each(function ($account) {
            ProcessAccountBalance::dispatch($account);
        });
    }

    private function updateMints(): void
    {
        $token = Token::findByZts(Token::ZTS_LP_ETH);
        $mints = TokenMint::where('token_id', $token->id)->get();
        $mints->each(function ($mint) {
            $block = $mint->account_block;
            $data = base64_decode($block->data->raw);
            $embeddedContract = new \DigitalSloth\ZnnPhp\Abi\Contracts\Token;
            $decoded = $embeddedContract->decode('Mint', $data);
            $parameters = $embeddedContract->getParameterNames('Mint');
            $parameters = explode(',', $parameters);
            $decodedData = array_combine(
                $parameters,
                $decoded
            );

            $mint->amount = $decodedData['amount'];
            $mint->save();
        });
    }

    private function updateBurns(): void
    {
        $token = Token::findByZts(Token::ZTS_LP_ETH);
        $burns = TokenBurn::where('token_id', $token->id)->get();
        $burns->each(function ($burn) {
            $block = $burn->account_block;
            $burn->amount = $block->amount;
            $burn->save();
        });
    }
};
