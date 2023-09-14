<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\PageController;
use App\Models\Nom\Account;
use App\Models\Nom\Token;

class Donate extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Donate';

        $donationAccount = Account::findByAddress(config('zenon.donation_address'));
        $excludeAddresses = Account::isEmbedded() // Exclude AZ + rewards
            ->orWhere('address', config('plasma-bot.address')) // Exclude refund from plasma bot
            ->pluck('id')->toArray();
        $ppToken = Token::findByZts('zts1hz3ys62vnc8tdajnwrz6pp'); // Ignore PP airdrop
        $donations = $donationAccount->received_blocks()
            ->whereNotIn('account_id', $excludeAddresses)
            ->whereNot('token_id', $ppToken->id)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $this->page['data']['account'] = $donationAccount;
        $this->page['data']['donations'] = $donations;

        return $this->render('pages/donate');
    }
}
