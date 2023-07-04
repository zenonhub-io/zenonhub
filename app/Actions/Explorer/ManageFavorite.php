<?php

namespace App\Actions\Explorer;

use App\Models\Markable\Favorite;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ManageFavorite
{
    public function __construct(
        protected Model $model,
        protected User $user,
        protected array $data
    ) {
    }

    public function execute(): void
    {
        if (Favorite::has($this->model, $this->user)) {
            Favorite::change($this->model, $this->user, null, [
                'label' => $this->data['label'] ?? null,
                'notes' => $this->data['notes'] ?? null,
            ]);
        } else {
            Favorite::add($this->model, $this->user, null, [
                'label' => $this->data['label'] ?? null,
                'notes' => $this->data['notes'] ?? null,
            ]);
        }
    }
}
