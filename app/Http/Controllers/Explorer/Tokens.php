<?php

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\Token;
use Meta;

class Tokens
{
    public function show()
    {
        Meta::title('Tokens')
            ->description('The list of ZTS Tokens, their supply and the number of holders in the Network of Momentum');

        return view('pages/explorer/overview', [
            'view' => 'explorer.tokens',
        ]);
    }

    public function detail($zts)
    {
        $token = Token::findByZts($zts);

        if (! $token) {
            abort(404);
        }

        Meta::title("{$token->name} ({$token->symbol}) - Token details")
            ->description("The {$token->name} ({$token->symbol}) token detail page shows total and current supply information, holder count and detailed lists of holders, transactions, mints and burns");

        return view('pages/explorer/detail', [
            'view' => 'explorer.token',
            'token' => $token,
        ]);
    }
}
