<?php

namespace App\Http\Controllers\Account;

use Meta;

class Overview
{
    public function show()
    {
        Meta::title('Manage your account');

        return view('pages/account');
    }
}
