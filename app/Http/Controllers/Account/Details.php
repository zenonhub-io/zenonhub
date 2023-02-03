<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\PageController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Details extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Account Details';
        $this->page['data'] = [
            'component' => 'account.details',
        ];

        return $this->render('pages/account');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => [
                'required',
                'max:255',
                'alpha_dash',
                Rule::unique(User::class)->ignore($request->user()->id)
            ],
            'email' => [
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($request->user()->id)
            ],
        ]);

        $user = $request->user();
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->save();

        return redirect()->route('account.details')
            ->with('alert' , [
                'type' => 'success',
                'message' => 'Account details updated',
                'icon' => 'check-circle-fill',
            ]);
    }
}
