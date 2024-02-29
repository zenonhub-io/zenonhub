<?php

namespace App\Livewire\Profile;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;
use Livewire\Component;

class UpdateDetails extends Component
{
    public array $state = [];

    public bool $verificationLinkSent = false;

    public function mount() : void
    {
        $user = Auth::user();

        $this->state = array_merge([
            'email' => $user?->email,
        ], $user?->withoutRelations()->toArray());
    }

    public function updateProfileInformation(UpdatesUserProfileInformation $updater) : void
    {
        $this->resetErrorBag();

        $updater->update(Auth::user(), $this->state);

        $this->dispatch('profile.details.saved');
    }

    public function sendEmailVerification() : void
    {
        Auth::user()?->sendEmailVerificationNotification();

        $this->verificationLinkSent = true;
    }

    public function getUserProperty() : ?Authenticatable
    {
        return Auth::user();
    }

    public function render() : View
    {
        return view('livewire.profile.update-details');
    }
}
