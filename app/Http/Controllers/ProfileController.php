<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class ProfileController
{
    private string $defaultTab = 'details';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Manage your account');

        return view('profile', [
            'tab' => $tab ?: $this->defaultTab,
        ]);
    }
}
