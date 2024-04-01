<?php

declare(strict_types=1);

namespace App\Http\Livewire\Modals\Explorer;

use App\Actions\Explorer\DeleteFavorite;
use App\Actions\Explorer\ManageFavorite;
use App\Domains\Nom\Models\Account;
use App\Models\Markable\Favorite;
use Livewire\Component;

class ManageFavoriteAccount extends Component
{
    public string $address;

    public ?string $label;

    public ?string $notes;

    public ?bool $exists;

    public function mount(string $address)
    {
        $account = Account::findBy('address', $address);
        $favorite = Favorite::findExisting($account, auth()->user());

        $this->address = $address;
        $this->exists = (bool) $favorite;
        $this->label = ($favorite ? $favorite->label : $account?->custom_label);
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
        $account = Account::findBy('address', $validatedData['address']);

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
        $account = Account::findBy('address', $validatedData['address']);

        (new DeleteFavorite($account, $user))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }
}
