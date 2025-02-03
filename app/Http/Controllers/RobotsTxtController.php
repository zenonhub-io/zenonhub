<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Nom\Account;
use App\Models\Nom\Token;
use Illuminate\Contracts\View\View;

class RobotsTxtController
{
    public function __invoke(): View
    {
        $allowedAccounts = Account::whereEmbedded()->pluck('address')->toArray();
        $allowedTokens = Token::whereNetwork()->pluck('token_standard')->toArray();

        return view('robots', [
            'sitemap' => route('sitemap'),
            'allowedAccounts' => $allowedAccounts,
            'allowedTokens' => $allowedTokens,
        ]);
    }
}
