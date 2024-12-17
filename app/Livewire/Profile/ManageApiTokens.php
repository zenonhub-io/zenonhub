<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Traits\Livewire\ConfirmsPasswordTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\NewAccessToken;
use Livewire\Component;

class ManageApiTokens extends Component
{
    use ConfirmsPasswordTrait;

    public array $createApiTokenForm = [
        'name' => '',
    ];

    public ?string $plainTextToken;

    public int $apiTokenIdBeingDeleted;

    public function createApiToken(): void
    {
        $this->resetErrorBag();

        Validator::make([
            'name' => $this->createApiTokenForm['name'],
        ], [
            'name' => ['required', 'string', 'max:255'],
        ])->validateWithBag('createApiToken');

        $this->displayTokenValue($this->user->createToken(
            $this->createApiTokenForm['name']
        ));

        $this->createApiTokenForm['name'] = '';

        $this->dispatch('profile.api-token.created');
    }

    public function confirmApiTokenDeletion($tokenId): void
    {
        $this->apiTokenIdBeingDeleted = $tokenId;
        $this->dispatch('show-inline-modal', id: 'confirm-delete-token');
    }

    public function deleteApiToken(): void
    {
        $this->user->tokens()->where('id', $this->apiTokenIdBeingDeleted)->first()->delete();
        $this->user->load('tokens');
        $this->dispatch('hide-inline-modal', id: 'confirm-delete-token');
    }

    public function getUserProperty(): Authenticatable
    {
        return Auth::user();
    }

    public function render(): View
    {
        return view('livewire.profile.manage-api-tokens');
    }

    protected function displayTokenValue(NewAccessToken $token): void
    {
        $this->plainTextToken = explode('|', $token->plainTextToken, 2)[1];

        $this->dispatch('show-inline-modal', id: 'view-api-token');
    }
}
