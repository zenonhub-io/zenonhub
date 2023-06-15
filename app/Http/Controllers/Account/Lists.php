<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PageController;
use Illuminate\Http\Request;

class Lists extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Account Lists';
        $this->page['data'] = [
            'component' => 'account.lists',
        ];

        return $this->render('pages/account');
    }

    public function store(Request $request)
    {
        $request->validate([
            'current_password' => 'current_password',
            'password' => 'required|confirmed',
        ]);

        $user = $request->user();
        $user->password = $request->input('password');
        $user->save();

        return redirect()->route('account.lists')
            ->with('alert', [
                'type' => 'success',
                'message' => 'Your password has been changed',
                'icon' => 'check-circle-fill',
            ]);
    }
}
