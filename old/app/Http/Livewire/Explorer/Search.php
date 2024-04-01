<?php

declare(strict_types=1);

namespace App\Http\Livewire\Explorer;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Token;
use Livewire\Component;

class Search extends Component
{
    public $search;

    public bool $error = false;

    protected $results;

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.search', [
            'results' => $this->results,
            'error' => $this->error,
        ]);
    }

    private function loadData()
    {
        if ($this->search) {
            $token = Token::whereListSearch($this->search)->first();
            if ($token) {
                return redirect()->route('explorer.token', ['zts' => $token->token_standard]);
            }

            $account = Account::whereListSearch($this->search)->first();
            if ($account) {
                return redirect()->route('explorer.account', ['address' => $account->address]);
            }

            $transaction = AccountBlock::whereListSearch($this->search)->first();
            if ($transaction) {
                return redirect()->route('explorer.transaction', ['hash' => $transaction->hash]);
            }

            $momentum = Momentum::whereListSearch($this->search)->first();
            if ($momentum) {
                return redirect()->route('explorer.momentum', ['hash' => $momentum->hash]);
            }

            $this->error = true;
        }
    }
}
