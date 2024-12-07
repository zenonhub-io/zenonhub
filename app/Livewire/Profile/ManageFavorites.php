<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\Nom\Account;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ManageFavorites extends Component
{
    protected $listeners = [
        'favorite.deleted' => '$refresh',
        'favorite.updated' => '$refresh',
    ];

    public function getUserProperty(): Authenticatable
    {
        return Auth::user();
    }

    public function render(): View
    {
        $favorites = Account::whereHasFavorite(auth()->user())->get();

        return view('livewire.profile.manage-favorites', [
            'favoritesAccounts' => $favorites,
        ]);
    }
}
