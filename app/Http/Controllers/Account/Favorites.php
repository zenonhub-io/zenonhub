<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;

class Favorites extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Account Favorites';
        $this->page['data'] = [
            'component' => 'account.favorites',
        ];

        return $this->render('pages/account');
    }
}
