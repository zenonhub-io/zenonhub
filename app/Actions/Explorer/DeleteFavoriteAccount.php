<?php

namespace App\Actions\Explorer;

use App\Models\Markable\Favorite;
use App\Models\Nom\Account;
use App\Models\User;

class DeleteFavoriteAccount
{
    public function __construct(
        protected Account $account,
        protected User $user,
    ) {
    }

    public function execute(): void
    {
        Favorite::remove($this->account, $this->user);
    }
}
