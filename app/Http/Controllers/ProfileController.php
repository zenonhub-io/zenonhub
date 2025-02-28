<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use MetaTags;

class ProfileController
{
    public function __invoke(?string $tab = 'details'): View
    {
        MetaTags::title(__('Manage Your Account'))
            ->description(__('Access and update your personal details, settings, and account preferences on Zenon Hub'))
            ->canonical(route('profile', ['tab' => $tab]))
            ->metaByName('robots', 'noindex,nofollow');

        return view('profile', [
            'tab' => $tab,
        ]);
    }
}
