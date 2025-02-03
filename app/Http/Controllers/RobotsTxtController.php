<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Nom\Account;
use App\Models\Nom\Token;
use Illuminate\Support\Facades\Response;

class RobotsTxtController
{
    public function __invoke(): \Illuminate\Http\Response
    {
        $allowedAccounts = Account::whereEmbedded()->pluck('address')->toArray();
        $allowedTokens = Token::whereNetwork()->pluck('token_standard')->toArray();

        $content = view('robots', [
            'sitemap' => route('sitemap'),
            'allowedAccounts' => $allowedAccounts,
            'allowedTokens' => $allowedTokens,
        ])->render();

        return Response::make($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
