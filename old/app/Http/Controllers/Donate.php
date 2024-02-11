<?php

namespace App\Http\Controllers;

use App\Models\Nom\Account;
use Meta;

class Donate
{
    public function show()
    {
        Meta::title('Donate to Zenon Hub', false);

        $donationAccount = Account::findByAddress(config('zenon.donation_address'));
        $excludeAddresses = Account::isEmbedded() // Exclude AZ + rewards
            ->orWhere('address', config('plasma-bot.address')) // Exclude refund from plasma bot
            ->pluck('id')
            ->toArray();

        $donations = $donationAccount->received_blocks()
            ->whereNotIn('account_id', $excludeAddresses)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('pages/donate', [
            'donations' => $donations,
        ]);
    }
}
