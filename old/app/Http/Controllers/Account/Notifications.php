<?php

namespace App\Http\Controllers\Account;

use Meta;

class Notifications
{
    public function show()
    {
        Meta::title('Manage your notifications');

        return view('pages/account', [
            'view' => 'account.notifications',
        ]);
    }
}
