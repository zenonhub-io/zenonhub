<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;

class Details extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Your Account';
        $this->page['data'] = [
            'component' => 'account.details',
        ];

        return $this->render('pages/account');
    }
}
