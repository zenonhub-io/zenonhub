<?php

namespace App\Http\Livewire\Explorer;

use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Momentum;
use App\Models\Nom\Token;
use Livewire\Component;

class Search extends Component
{
    public $search;
    protected $results;
    public bool $error = false;

    public function render()
    {
        $this->loadData();

        return view('livewire.explorer.search', [
            'results' => $this->results,
            'error' => $this->error
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
