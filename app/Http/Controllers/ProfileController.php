<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class ProfileController
{
    public function __invoke(?string $tab = 'details'): View
    {
        MetaTags::title('Manage your account');

        return view('profile', [
            'tab' => $tab,
        ]);
    }
}
