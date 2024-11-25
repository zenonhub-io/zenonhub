<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Token;
use Illuminate\Contracts\View\View;
use MetaTags;

class TokenDetailController
{
    private string $defaultTab = 'holders';

    public function __invoke(string $zts, ?string $tab = null): View
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
            ->description(__('The :name (:symbol) token detail page shows total and current supply information, holder count and detailed lists of holders, transactions, mints and burns', ['name' => $token->name, 'symbol' => $token->symbol]));

        $tab = $tab ?: $this->defaultTab;

        return view('explorer.token-details', [
            'tab' => $tab,
            'token' => $token,
        ]);
    }
}
