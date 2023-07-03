<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;
use App\Models\Nom\Account;

class Accounts extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Accounts';
        $this->page['meta']['description'] = 'A list of all addresses on the Network of Momentum, this includes embedded contracts, pillars, sentinels and wallets';
        $this->page['data'] = [
            'component' => 'explorer.accounts',
        ];

        return $this->render('pages/explorer/overview');
    }

    public function detail($address)
    {
        $account = Account::findByAddress($address);

        if (! $account) {
            abort(404);
        }

        $this->page['meta']['title'] = 'Account Details'.($account->has_custom_label ? ' | '.$account->custom_label : '');
        $this->page['meta']['description'] = "The address {$account->address} page shows an overview of the address and detailed list of transactions, rewards, delegations, token holdings, staking, fusions and projects";
        $this->page['data'] = [
            'component' => 'explorer.account',
            'account' => $account,
        ];

        return $this->render('pages/explorer/detail');
    }
}
