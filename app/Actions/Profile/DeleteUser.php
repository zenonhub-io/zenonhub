<?php

namespace App\Actions\Profile;

use App\Models\User;

class DeleteUser
{
    public function delete(User $user) : void
    {
        $user->tokens->each->delete();
        $user->delete();
    }
}
