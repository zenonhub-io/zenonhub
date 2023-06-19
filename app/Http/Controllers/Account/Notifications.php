<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;

class Notifications extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Account Notifications';
        $this->page['data'] = [
            'component' => 'account.notifications',
        ];

        return $this->render('pages/account');
    }
}
