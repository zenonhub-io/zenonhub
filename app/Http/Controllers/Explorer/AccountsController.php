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
            ->description('The top account addresses in the Zenon Network in descending order by the amount of Zenon (ZNN) each account holds')
            ->canonical(route('explorer.account.list', ['tab' => $tab]))
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.account-list', [
            'tab' => $tab,
        ]);
    }

    public function show(string $address, ?string $tab = 'transactions'): View
    {
        $account = Account::where('address', $address)
            ->withCount('sentBlocks')
            ->withCount(['tokens as tokens_count' => fn ($query) => $query->where('balance', '>', '0')])
            ->first();

        if (! $account) {
            abort(404);
        }

        $metaTitle = collect([
            ($account->has_custom_label ? $account->custom_label : null),
            'Address ' . $account->address . ' details',
        ])->filter()->implode(' | ');

        $metaDescription = $account->is_embedded_contract
            ? __('Explore detailed information about the embedded :contract on Zenon, including balances and transactions.', ['contract' => $account->name])
            : __('This page contains details about a Zenon account (:address), including balances, token holdings, and transaction history, rewards and Accelerator-Z projects', ['address' => $account->address]);

        MetaTags::title($metaTitle)
            ->description($metaDescription)
            ->canonical(route('explorer.account.detail', ['address' => $account->address]))
            ->metaByName('robots', $account->is_embedded_contract && $tab === 'transactions' ? 'index,nofollow' : 'noindex,nofollow');

        return view('explorer.account-details', [
            'tab' => $tab,
            'account' => $account,
        ]);
    }
}
