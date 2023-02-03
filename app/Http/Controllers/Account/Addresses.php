<?php

namespace App\Http\Controllers\Account;

use App\Classes\Utilities;
use App\Http\Controllers\PageController;
use App\Models\Nom\Account;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Http\Request;

class Addresses extends PageController
{
    public function show(Request $request)
    {
        $this->page['meta']['title'] = 'Account Addresses';
        $this->page['data'] = [
            'component' => 'account.addresses',
        ];

        return $this->render('pages/account');
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => [
                'required',
                'string',
                'exists:nom_accounts,address'
            ],
            'nickname' => [
                'string',
                'nullable'
            ],
            'message' => [
                'required',
                'string',
            ],
            'signature' => [
                'required',
                'string',
            ],
        ]);

        $account = Account::findByAddress($request->input('address'));

        $verified = ZnnUtilities::verifySignedMessage(
            $account->decoded_public_key,
            $request->input('message'),
            $request->input('signature')
        );

        if ($verified) {
            $user = $request->user();

            if ($request->input('default')) {
                $user->accounts()->each(function ($account) {
                    $account->pivot->is_default = false;
                    $account->pivot->save();
                });
            }

            $accountIds = $user->accounts()->pluck('account_id')->toArray();

            if (! in_array($account->id, $accountIds)) {
                $user->accounts()->attach($account, [
                    'nickname' => $request->input('nickname'),
                    'is_pillar' => Utilities::isPillar($account->address),
                    'is_sentinel' => Utilities::isSentinel($account->address),
                    'is_default' => $request->input('default', false),
                    'verified_at' => now(),
                ]);
            } else {
                $user->accounts()->updateExistingPivot($account->id, [
                    'nickname' => $request->input('nickname'),
                    'is_pillar' => Utilities::isPillar($account->address),
                    'is_sentinel' => Utilities::isSentinel($account->address),
                    'is_default' => $request->input('default', false),
                ]);
            }

            return redirect()->route('account.addresses')
                ->with('alert' , [
                    'type' => 'success',
                    'message' => 'Address verified!',
                    'icon' => 'check-circle-fill',
                ]);
        } else {
            return back()
                ->withErrors(['signature' => 'Invalid signature provided']);
        }
    }
}
