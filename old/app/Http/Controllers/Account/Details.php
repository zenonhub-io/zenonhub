<?php

namespace App\Http\Controllers\Account;

use Meta;

class Details
{
    public function show()
    {
        Meta::title('Manage your details');

        return view('pages/account', [
            'view' => 'account.details',
        ]);
    }
}
