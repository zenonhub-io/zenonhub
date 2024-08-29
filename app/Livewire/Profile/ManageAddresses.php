<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Services\ZenonSdk;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class ManageAddresses extends Component
{
    public array $verifyAddressForm = [
        'nickname' => '',
        'address' => '',
        'public_key' => '',
        'message' => '',
        'signature' => '',
    ];

    public bool $confirmingAddressDeletion = false;

    public string $addressBeingDeleted;

    public function verifyAddress(): void
    {
        $this->resetErrorBag();

        Validator::make([
            'address' => $this->verifyAddressForm['address'],
            'nickname' => $this->verifyAddressForm['nickname'],
            'message' => $this->verifyAddressForm['message'],
            'signature' => $this->verifyAddressForm['signature'],
            //'public_key' => $this->verifyAddressForm['public_key'],
        ], [
            'address' => ['required', 'string', 'max:40', 'exists:nom_accounts,address'],
            'nickname' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:8'],
            'signature' => ['required', 'string'],
            //'public_key' => ['required', 'string'],
        ])->validateWithBag('verifyAddress');

        $zenonSdk = app(ZenonSdk::class);
        $account = load_account($this->verifyAddressForm['address']);

        $validated = $zenonSdk->verifySignature(
            $account->decoded_public_key,
            $account->address,
            $this->verifyAddressForm['message'],
            $this->verifyAddressForm['signature'],
        );

        if (! $validated) {

            $this->setRandomMessage();
            $this->verifyAddressForm['signature'] = '';

            throw ValidationException::withMessages([
                'signature' => [__('Unable to verify address, please try again')],
            ]);
        }

        $this->user->verifiedAccounts()->syncWithoutDetaching([
            $account->id => [
                'nickname' => $this->verifyAddressForm['nickname'],
                'verified_at' => now(),
            ],
        ]);

        $this->verifyAddressForm['address'] = '';
        $this->verifyAddressForm['nickname'] = '';
        $this->verifyAddressForm['message'] = '';
        $this->verifyAddressForm['signature'] = '';

        $this->setRandomMessage();

        $this->dispatch('profile.address.verified');
    }

    public function confirmAddressDeletion($address): void
    {
        $this->addressBeingDeleted = $address;
        $this->dispatch('show-inline-modal', id: 'confirm-delete-address');
    }

    public function deleteAddress(): void
    {
        $account = $this->user
            ->verifiedAccounts()
            ->where('address', $this->addressBeingDeleted)
            ->first();

        if ($account) {
            $this->user
                ->verifiedAccounts()
                ->detach($account->id);
        }

        $this->dispatch('hide-inline-modal', id: 'confirm-delete-address');
    }

    public function getUserProperty(): Authenticatable
    {
        return Auth::user();
    }

    public function render(): View
    {
        return view('livewire.profile.manage-addresses');
    }

    public function mount(): void
    {
        $this->setRandomMessage();
    }

    private function setRandomMessage(): void
    {
        $this->verifyAddressForm['message'] = app()->isProduction()
            ? Str::upper(Str::random(8))
            : 'TEST';
    }
}
