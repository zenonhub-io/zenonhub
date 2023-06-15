<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;

class Overview extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Mange your account';

        return $this->render('pages/account');
    }
}
