<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Nom\Account;
use Illuminate\Contracts\View\View;
use MetaTags;

class DonateController
{
    public function __invoke(): View
    {
        MetaTags::title(__('Donate to Zenon Hub: Support Our Mission'), false)
            ->description(__('Contribute to Zenon Hub and help us continue building innovative tools for the Network of Momentum'))
            ->canonical(route('donate'))
            ->metaByName('robots', 'index,nofollow');

        $donationAccount = Account::firstWhere('address', config('zenon-hub.donation_address'));
        $excludeAddresses = Account::whereEmbedded() // Exclude AZ + rewards
            ->orWhere('address', config('services.plasma-bot.address')) // Exclude refund from plasma bot
            ->pluck('id')
            ->toArray();

        $donations = $donationAccount->receivedBlocks()->with(['account', 'token'])
            ->whereNotIn('account_id', $excludeAddresses)
            ->whereIn('token_id', [app('znnToken')->id, app('qsrToken')->id])
            ->orderBy('id', 'desc')
            ->get();

        return view('donate', [
            'donationAccount' => $donationAccount,
            'donations' => $donations,
        ]);
    }
}
