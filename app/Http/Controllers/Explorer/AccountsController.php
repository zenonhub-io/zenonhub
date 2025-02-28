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
        if ($tab === 'all') {
            $title = __('All Accounts: Zenon Network Account Leaderboard');
            $description = __('View top Zenon accounts ranked by their balance holdings in descending order');
            $canonical = route('explorer.account.list');
        } else {
            $title = __(':tab Accounts: Zenon Network Account Leaderboard', ['tab' => str($tab)->singular()->title()]);
            $description = __('Explore Zenon :tab accounts ranked by their holdings, listed in descending balance order', ['tab' => $tab]);
            $canonical = route('explorer.account.list', ['tab' => $tab]);
        }

        MetaTags::title($title)
            ->description($description)
            ->canonical($canonical)
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

        $title = $account->is_embedded_contract
            ? __(':name | Embedded Contract Details', ['name' => $account->name])
            : __('Address :name | Zenon Account Details', ['name' => short_hash($account->address)]);

        $description = $account->is_embedded_contract
            ? __('Get insights on the embedded :contract in Zenon, including balances and transaction details', ['contract' => $account->name])
            : __('Discover details about Zenon account (:address), including balances, token holdings, rewards, transactions, and Accelerator-Z projects', ['address' => $account->address]);

        MetaTags::title($title)
            ->description($description)
            ->canonical(route('explorer.account.detail', ['address' => $account->address]))
            ->metaByName('robots', $account->is_embedded_contract && $tab === 'transactions' ? 'index,nofollow' : 'noindex,nofollow');

        return view('explorer.account-details', [
            'tab' => $tab,
            'account' => $account,
        ]);
    }
}
