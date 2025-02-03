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
        MetaTags::title('Tokens')
            ->description('The list of ZTS Tokens, their supply and the number of holders in the Network of Momentum')
            ->canonical(route('explorer.token.list', ['tab' => $tab]))
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.token-list', [
            'tab' => $tab,
        ]);
    }

    public function show(string $zts, ?string $tab = 'holders'): View
    {
        $token = Token::where('token_standard', $zts)
            ->with('owner')
            ->withCount(['holders as holders_count' => fn ($query) => $query->where('balance', '>', '0')])
            ->withSum('mints as total_minted', 'amount')
            ->withSum('burns as total_burned', 'amount')
            ->first();

        if (! $token) {
            abort(404);
        }

        MetaTags::title(__(':name (:symbol) - Token details', ['name' => $token->name, 'symbol' => $token->symbol]))
            ->description(__('The :name (:symbol) token detail page shows total and current supply information, holder count and detailed lists of holders, transactions, mints and burns', ['name' => $token->name, 'symbol' => $token->symbol]))
            ->canonical(route('explorer.token.detail', ['zts' => $token->token_standard]))
            ->metaByName('robots', $token->is_network && $tab === 'holders' ? 'index,nofollow' : 'noindex,nofollow');

        return view('explorer.token-details', [
            'tab' => $tab,
            'token' => $token,
        ]);
    }
}
