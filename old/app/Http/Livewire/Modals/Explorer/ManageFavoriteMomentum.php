<?php

namespace App\Http\Livewire\Modals\Explorer;

use App\Actions\Explorer\DeleteFavorite;
use App\Actions\Explorer\ManageFavorite;
use App\Models\Markable\Favorite;
use App\Models\Nom\Momentum;
use Livewire\Component;

class ManageFavoriteMomentum extends Component
{
    public string $hash;

    public ?string $notes;

    public ?bool $exists;

    public function mount(string $hash)
    {
        $momentum = Momentum::findByHash($hash);
        $favorite = Favorite::findExisting($momentum, auth()->user());

        $this->hash = $hash;
        $this->exists = (bool) $favorite;
        $this->notes = $favorite?->notes;
    }

    public function render()
    {
        return view('livewire.modals.explorer.manage-favorite-momentum');
    }

    public function onAddFavorite()
    {
        $validatedData = $this->validate([
            'hash' => [
                'required',
                'exists:nom_momentums,hash',
            ],
            'notes' => [
                'required',
            ],
        ]);

        $user = auth()->user();
        $momentum = Momentum::findByHash($validatedData['hash']);

        (new ManageFavorite($momentum, $user, $validatedData))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }

    public function onDeleteFavorite()
    {
        $validatedData = $this->validate([
            'hash' => [
                'required',
                'exists:nom_momentums,hash',
            ],
        ]);

        $user = auth()->user();
        $momentum = Momentum::findByHash($validatedData['hash']);

        (new DeleteFavorite($momentum, $user))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }
}
