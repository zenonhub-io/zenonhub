<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Actions\Profile\DeleteUser as DeleteUserAction;
use App\Traits\Livewire\ConfirmsPasswordTrait;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DeleteUser extends Component
{
    use ConfirmsPasswordTrait;

    public function deleteUser(Request $request, DeleteUserAction $deleter, StatefulGuard $auth): Redirector
    {
        $this->ensurePasswordIsConfirmed();

        $deleter->delete(Auth::user()->fresh());

        $auth->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect(config('fortify.redirects.logout') ?? '/');
    }

    public function render(): View
    {
        return view('livewire.profile.delete-user');
    }
}
