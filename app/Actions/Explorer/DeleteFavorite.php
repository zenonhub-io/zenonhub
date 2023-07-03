<?php

namespace App\Actions\Explorer;

use App\Models\Markable\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DeleteFavorite
{
    public function __construct(
        protected Model $model,
        protected User $user,
    ) {
    }

    public function execute(): void
    {
        Favorite::remove($this->model, $this->user);
    }
}
