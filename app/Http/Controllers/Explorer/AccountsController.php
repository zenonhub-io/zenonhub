<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Account;
use Illuminate\Contracts\View\View;
use MetaTags;

class AccountsController
{
    public function index(?string $tab = 'all'): View
    {
        MetaTags::title('Accounts')
            ->description('The top account addresses in the Zenon Network in descending order by the amount of Zenon (ZNN) each account holds');

        return view('explorer.account-list', [
            'tab' => $tab,
        ]);
    }

    public function show(string $address, ?string $tab = 'transactions'): View
    {
        $account = Account::where('address', $address)
            ->first();

        if (! $account) {
            abort(404);
        }

        $metaTitle = collect([
            ($account->has_custom_label ? $account->custom_label : null),
            'Address ' . $account->address . ' details',
        ])->filter()->implode(' | ');

        MetaTags::title($metaTitle)
            ->description(__('he Address :address page shows key account details including balances, total rewards, plasma as well as detailed lists of transactions, rewards, delegations, token holdings, stakes, fusions and projects', ['address' => $account->address]));

        return view('explorer.account-details', [
            'tab' => $tab,
            'account' => $account,
        ]);
    }
}
