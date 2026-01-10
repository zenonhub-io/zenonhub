<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Token;
use Illuminate\Contracts\View\View;
use MetaTags;

class TokensController
{
    public function index(?string $tab = 'all'): View
    {
        if ($tab === 'all') {
            $title = __('All Tokens List: Supply & Holders in the Zenon Network');
            $description = __('Explore all tokens on the Zenon Network. View their total supply, current supply, and detailed holder statistics in one place');
            $canonical = route('explorer.token.list');
        } else {
            $title = __(':tab Tokens List: Supply & Holders in the Zenon Network', ['tab' => str($tab)->singular()->title()]);
            $description = __('Discover :tab tokens in the Zenon Network. Learn about their supply, holders, and key statistics for deeper insights', ['tab' => $tab]);
            $canonical = route('explorer.token.list', ['tab' => $tab]);
        }

        MetaTags::title($title)
            ->description($description)
            ->canonical($canonical)
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.token-list', [
            'tab' => $tab,
        ]);
    }

    public function show(string $zts, ?string $tab = 'holders'): View
    {
        $token = Token::where('token_standard', $zts)
            ->with(['owner'])
            ->withCount(['holders as holders_count' => fn ($query) => $query->where('balance', '>', '0')])
            ->withSum('mints as total_minted', 'amount')
            ->withSum('burns as total_burned', 'amount')
            ->first();

        if (! $token) {
            abort(404);
        }

        MetaTags::title(__(':name (:symbol) - Token Details & Statistics', ['name' => $token->name, 'symbol' => $token->symbol]))
            ->description(__('Discover detailed statistics for the :name (:symbol) token, including total and current supply, holder count, transactions, and mint/burn records on the Zenon Network', ['name' => $token->name, 'symbol' => $token->symbol]))
            ->canonical(route('explorer.token.detail', ['zts' => $token->token_standard]))
            ->metaByName('robots', $token->is_network && $tab === 'holders' ? 'index,follow' : 'noindex,follow');

        return view('explorer.token-details', [
            'tab' => $tab,
            'token' => $token,
        ]);
    }
}
