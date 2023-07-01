<?php

namespace App\Actions\Explorer;

use App\Models\Markable\Favorite;
use App\Models\Nom\Account;
use App\Models\User;

class ManageFavoriteAccount
{
    public function __construct(
        protected Account $account,
        protected User $user,
        protected array $data
    ) {
    }

    public function execute(): void
    {
        if (Favorite::has($this->account, $this->user)) {
            Favorite::change($this->account, $this->user, null, [
                'label' => $this->data['label'],
                'notes' => $this->data['notes'],
            ]);
        } else {
            Favorite::add($this->account, $this->user, null, [
                'label' => $this->data['label'],
                'notes' => $this->data['notes'],
            ]);
        }
    }
}
