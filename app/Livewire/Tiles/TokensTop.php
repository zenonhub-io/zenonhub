<?php

declare(strict_types=1);

namespace App\Livewire\Tiles;

use App\Livewire\BaseComponent;
use App\Models\Nom\Token;

class TokensTop extends BaseComponent
{
    public function render()
    {
        return view('livewire.tiles.tokens-top', [
            'tokens' => Token::withCount(['holders as holders_count' => fn ($query) => $query->where('balance', '>', '0')])
                ->orderBy('holders_count', 'desc')
                ->limit(5)
                ->get(),
        ]);
    }
}
