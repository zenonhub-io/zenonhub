<?php

namespace App\Http\Livewire\Modals\Explorer;

use App\Actions\Explorer\DeleteFavorite;
use App\Actions\Explorer\ManageFavorite;
use App\Models\Markable\Favorite;
use App\Models\Nom\AccountBlock;
use Livewire\Component;

class ManageFavoriteTransaction extends Component
{
    public string $hash;

    public ?string $notes;

    public ?bool $exists;

    public function mount(string $hash)
    {
        $block = AccountBlock::findByHash($hash);
        $favorite = Favorite::findExisting($block, auth()->user());

        $this->hash = $hash;
        $this->exists = (bool) $favorite;
        $this->notes = $favorite?->notes;
    }

    public function render()
    {
        return view('livewire.modals.explorer.manage-favorite-transaction');
    }

    public function onAddFavorite()
    {
        $validatedData = $this->validate([
            'hash' => [
                'required',
                'exists:nom_account_blocks,hash',
            ],
            'notes' => [
                'nullable',
            ],
        ]);

        $user = auth()->user();
        $block = AccountBlock::findByHash($validatedData['hash']);

        (new ManageFavorite($block, $user, $validatedData))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }

    public function onDeleteFavorite()
    {
        $validatedData = $this->validate([
            'hash' => [
                'required',
                'exists:nom_account_blocks,hash',
            ],
        ]);

        $user = auth()->user();
        $account = AccountBlock::findByHash($validatedData['hash']);

        (new DeleteFavorite($account, $user))->execute();

        $this->emit('hideModal');
        $this->emit('refreshPage');
    }
}
