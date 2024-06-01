<?php

declare(strict_types=1);

namespace App\Http\Controllers\Account;

use App\Classes\Utilities;
use App\Domains\Nom\Models\Account;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Http\Request;

class Addresses
{
    public function show()
    {
        $this->page['meta']['title'] = 'Account Addresses';
        $this->page['data'] = [
            'view' => 'account.addresses',
        ];

        return view('pages/account');
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => [
                'required',
                'string',
                'exists:nom_accounts,address',
            ],
            'nickname' => [
                'string',
                'nullable',
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

        $account = Account::firstWhere('address', $request->input('address'));

        $verified = ZnnUtilities::verifySignedMessage(
            $account->decoded_public_key,
            $request->input('message'),
            $request->input('signature')
        );

        if (! $verified) {
            return back()
                ->withErrors(['signature' => 'Invalid signature provided']);
        }
        $user = $request->user();

        if ($request->input('default')) {
            $user->nom_accounts()->each(function ($account) {
                $account->pivot->is_default = false;
                $account->pivot->save();
            });
        }

        $accountIds = $user->nom_accounts()->pluck('account_id')->toArray();

        if (! in_array($account->id, $accountIds)) {
            $user->nom_accounts()->attach($account, [
                'nickname' => $request->input('nickname'),
                'is_pillar' => Utilities::isPillar($account->address),
                'is_sentinel' => Utilities::isSentinel($account->address),
                'is_default' => $request->input('default', false),
                'verified_at' => now(),
            ]);
        } else {
            $user->nom_accounts()->updateExistingPivot($account->id, [
                'nickname' => $request->input('nickname'),
                'is_pillar' => Utilities::isPillar($account->address),
                'is_sentinel' => Utilities::isSentinel($account->address),
                'is_default' => $request->input('default', false),
            ]);
        }

        return redirect()->route('account.addresses')
            ->with('alert', [
                'type' => 'success',
                'message' => 'Address verified!',
                'icon' => 'check-circle-fill',
            ]);
    }
}
