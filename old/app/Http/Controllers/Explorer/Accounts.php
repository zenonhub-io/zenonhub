<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Domains\Nom\Models\Account;
use Meta;

class Accounts
{
    public function show()
    {
        Meta::title('Zenon Network Top Accounts')
            ->description('The top account addresses in the Zenon Network in descending order by the amount of Zenon (ZNN) each account holds');

        return view('pages/explorer/overview', [
            'view' => 'explorer.accounts',
        ]);
    }

    public function detail($address)
    {
        $account = Account::findBy('address', $address);

        if (! $account) {
            abort(404);
        }

        $metaTitle = collect([
            ($account->has_custom_label ? $account->custom_label : null),
            'Address ' . $account->address . ' details',
        ])->filter()->implode(' | ');

        Meta::title($metaTitle)
            ->description("The Address {$account->address} page shows key account details including balances, total rewards, plasma as well as detailed lists of transactions, rewards, delegations, token holdings, stakes, fusions and projects");

        return view('pages/explorer/detail', [
            'view' => 'explorer.account',
            'account' => $account,
        ]);
    }
}
