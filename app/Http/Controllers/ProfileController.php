<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class ProfileController
{
    protected string $defaultTab = 'details';

    public function __invoke($tab = null) : View
    {
        MetaTags::title('Manage your account');

        return view('profile', [
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
