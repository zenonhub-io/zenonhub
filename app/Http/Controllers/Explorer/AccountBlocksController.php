<?php

declare(strict_types=1);

namespace App\Http\Controllers\Explorer;

use App\Models\Nom\AccountBlock;
use Illuminate\Contracts\View\View;
use MetaTags;

class AccountBlocksController
{
    public function index(): View
    {
        MetaTags::title(__('Zenon Network Account Blocks: Confirmed Transfers & Smart Contract Interactions'))
            ->description(__('Browse confirmed account blocks on the Zenon Network, including token transfers and embedded smart contract interactions'))
            ->canonical(route('explorer.block.list'))
            ->metaByName('robots', 'index,nofollow');

        return view('explorer.account-block-list');
    }

    public function show(string $hash, ?string $tab = 'data'): View
    {
        $block = AccountBlock::where('hash', $hash)
            ->with(['account', 'toAccount', 'token', 'momentum', 'parent', 'pairedAccountBlock', 'data'])
            ->withCount('descendants')
            ->first();

        if (! $block) {
            abort(404);
        }

        MetaTags::title(__('Account Block Details: Hash :hash', ['hash' => short_hash($block->hash)]))
            ->description(__('View detailed information for account block :hash, including status, block type, confirmation, and token transfer data', ['hash' => $block->hash]))
            ->canonical(route('explorer.block.detail', ['hash' => $block->hash]))
            ->metaByName('robots', 'noindex,nofollow');

        return view('explorer.account-block-details', [
            'tab' => $tab,
            'block' => $block,
        ]);
    }
}
