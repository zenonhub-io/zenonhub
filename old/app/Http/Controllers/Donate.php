<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Nom\Models\Account;
use Meta;

class Donate
{
    public function show()
    {
        Meta::title('Donate to Zenon Hub', false);

        $donationAccount = Account::findBy('address', config('zenon.donation_address'));
        $excludeAddresses = Account::isEmbedded() // Exclude AZ + rewards
            ->orWhere('address', config('plasma-bot.address')) // Exclude refund from plasma bot
            ->pluck('id')
            ->toArray();

        $donations = $donationAccount->receivedBlocks()
            ->whereNotIn('account_id', $excludeAddresses)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return view('pages/donate', [
            'donations' => $donations,
        ]);
    }
}