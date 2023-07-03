<?php

namespace App\Http\Livewire\Modals\Explorer;

use App\Actions\Explorer\DeleteFavorite;
use App\Actions\Explorer\ManageFavorite;
use App\Models\Markable\Favorite;
use App\Models\Nom\Account;
use Livewire\Component;

class ManageFavoriteAccount extends Component
{
    public string $address;

    public ?string $label;

    public ?string $notes;

    public ?bool $exists;

    public function mount(string $address)
    {
        $account = Account::findByAddress($address);
        $favorite = Favorite::findExisting($account, auth()->user());

        $this->address = $address;
        $this->exists = (bool) $favorite;
        $this->label = ($favorite ? $favorite->label : $account?->named_address);
        $this->notes = $favorite?->notes;
    }

    public function render()
    {
        return view('livewire.modals.explorer.manage-favorite-account');
    }

    public function onAddFavorite()
    {
        $validatedData = $this->validate([
            'address' => [
                'required',
                'exists:nom_accounts,address',
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
        $account = Account::findByAddress($validatedData['address']);

        (new ManageFavorite($account, $user, $validatedData))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }

    public function onDeleteFavorite()
    {
        $validatedData = $this->validate([
            'address' => [
                'required',
                'exists:nom_accounts,address',
            ],
        ]);

        $user = auth()->user();
        $account = Account::findByAddress($validatedData['address']);

        (new DeleteFavorite($account, $user))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }
}
