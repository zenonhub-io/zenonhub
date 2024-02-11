<?php

namespace App\Http\Controllers\Account;

use Meta;

class Favorites
{
    public function show()
    {
        Meta::title('Manage your favourites');

        return view('pages/account', [
            'view' => 'account.favorites',
        ]);
    }
}
