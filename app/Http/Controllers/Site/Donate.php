<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\PageController;
use App\Models\Nom\Account;

class Donate extends PageController
{
    public function show()
    {
        $this->page['meta']['title'] = 'Donate';

        $donationAccount = Account::findByAddress(config('zenon.donation_address'));
        $excludeAddresses = Account::isEmbedded()->pluck('id')->toArray();
        $donations = $donationAccount->received_blocks()
            ->whereNotIn('account_id', $excludeAddresses)
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $this->page['data']['account'] = $donationAccount;
        $this->page['data']['donations'] = $donations;

        return $this->render('pages/donate');
    }
}
