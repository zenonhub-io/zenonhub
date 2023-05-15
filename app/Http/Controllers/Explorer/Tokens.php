<?php

namespace App\Http\Controllers\Explorer;

use App\Http\Controllers\PageController;
use App\Models\Nom\Token;

class Tokens extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Tokens';
        $this->page['meta']['description'] = 'The list of ZTS Tokens, their supply and the number of holders in the Network of Momentum';
        $this->page['data'] = [
            'component' => 'explorer.tokens',
        ];

        return $this->render('pages/explorer/overview');
    }

    public function detail($zts)
    {
        $token = Token::findByZts($zts);

        if (! $token) {
            abort(404);
        }

        $this->page['meta']['title'] = 'Token Detail | '.$token->name;
        $this->page['meta']['description'] = "{$token->name} ({$token->symbol}) Token Details shows total and current supply information and list of holders, transactions, mints and burns";
        $this->page['data'] = [
            'component' => 'explorer.token',
            'token' => $token,
        ];

        return $this->render('pages/explorer/detail');
    }
}
