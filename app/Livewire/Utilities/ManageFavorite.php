<?php

declare(strict_types=1);

namespace App\Livewire\Utilities;

use App\Livewire\Profile\ManageFavorites;
use App\Models\Favorite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ManageFavorite extends Component
{
    public ?string $title = null;

    public ?string $itemType = null;

    public ?string $itemId = null;

    public ?Favorite $favorite = null;

    public ?Model $model;

    public bool $hasUserFavorite = false;

    public array $favoriteForm = [
        'value' => '',
        'label' => '',
        'notes' => '',
    ];

    public function render()
    {
        return view('livewire.utilities.manage-favorite');
    }

    public function mount(): void
    {
        $this->loadFavorite();
    }

    public function saveFavorite(): void
    {
        $this->resetErrorBag();

        $rules = [
            'label' => ['required', 'string', 'max:40'],
            'notes' => ['nullable', 'max:255'],
        ];

        Validator::make([
            'label' => $this->favoriteForm['label'],
            'notes' => $this->favoriteForm['notes'],
        ], $rules)->validateWithBag('saveFavorite');

        $favorite = Favorite::add($this->model, auth()->user());
        $favorite->label = $this->favoriteForm['label'];
        $favorite->notes = $this->favoriteForm['notes'];
        $favorite->save();

        $this->dispatch('hide-inline-modal', id: 'edit-favorite-address-' . $this->model->address);
        $this->dispatch('favorite.updated')->to(ManageFavorites::class);
    }

    public function deleteFavorite(): void
    {
        $this->resetErrorBag();

        Favorite::remove($this->model, auth()->user());

        $this->dispatch('hide-inline-modal', id: 'edit-favorite-address-' . $this->model->address);
        $this->dispatch('favorite.deleted')->to(ManageFavorites::class);
    }

    private function loadFavorite(): void
    {
        if ($this->favorite === null && $this->itemType) {

            $model = null;

            if ($this->itemType === 'address') {
                $model = load_account($this->itemId);
            }

            if ($this->itemType === 'token') {
                $model = load_token($this->itemId)?->load('owner');
            }

            $favorite = Favorite::findExisting($model, auth()->user());

            $this->model = $model;
            $this->favorite = $favorite;
            if ($favorite) {
                $this->favoriteForm = $favorite->toArray();
                $this->hasUserFavorite = true;
            } else {
                $this->favoriteForm['label'] = $model->address;
            }
        }
    }
}
