<?php

namespace App\Http\Livewire\Modals\Explorer;

use App\Actions\Explorer\DeleteFavorite;
use App\Actions\Explorer\ManageFavorite;
use App\Models\Markable\Favorite;
use App\Models\Nom\Token;
use Livewire\Component;

class ManageFavoriteToken extends Component
{
    public string $zts;

    public ?string $label;

    public ?string $notes;

    public ?bool $exists;

    public function mount(string $zts)
    {
        $token = Token::findByZts($zts);
        $favorite = Favorite::findExisting($token, auth()->user());

        $this->zts = $zts;
        $this->exists = (bool) $favorite;
        $this->label = ($favorite ? $favorite->label : $token?->name);
        $this->notes = $favorite?->notes;
    }

    public function render()
    {
        return view('livewire.modals.explorer.manage-favorite-token');
    }

    public function onAddFavorite()
    {
        $validatedData = $this->validate([
            'zts' => [
                'required',
                'exists:nom_tokens,token_standard',
            ],
            'label' => [
                'required',
                'max:255',
            ],
            'notes' => [
                'nullable',
            ],
        ]);

        $user = auth()->user();
        $token = Token::findByZts($validatedData['zts']);

        (new ManageFavorite($token, $user, $validatedData))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }

    public function onDeleteFavorite()
    {
        $validatedData = $this->validate([
            'zts' => [
                'required',
                'exists:nom_tokens,token_standard',
            ],
        ]);

        $user = auth()->user();
        $token = Token::findByZts($validatedData['zts']);

        (new DeleteFavorite($token, $user))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }
}
