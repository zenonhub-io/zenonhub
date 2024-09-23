<?php

declare(strict_types=1);

namespace App\Livewire\Utilities;

use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use App\Models\Nom\Token;
use App\Models\SocialProfile;
use App\Services\ZenonSdk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UpdateSocialProfile extends Component
{
    public string $title;

    public string $itemType;

    public string $itemId;

    public ?SocialProfile $socialProfile = null;

    public string $address = '';

    public string $message = '';

    public string $signature = '';

    public array $socialProfileForm = [
        'name' => '',
        'bio' => '',
        'avatar' => '',
        'website' => '',
        'email' => '',
        'x' => '',
        'telegram' => '',
        'github' => '',
        'medium' => '',
        'discord' => '',
    ];

    public function render()
    {
        return view('livewire.utilities.update-social-profile');
    }

    public function mount(): void
    {
        $this->loadSocialProfile();
        $this->setRandomMessage();
    }

    public function saveProfile(): void
    {
        $this->resetErrorBag();

        Validator::make([
            'address' => $this->address,
            'message' => $this->message,
            'signature' => $this->signature,

            'bio' => $this->socialProfileForm['bio'],
            'avatar' => $this->socialProfileForm['avatar'],
            'website' => $this->socialProfileForm['website'],
            'email' => $this->socialProfileForm['email'],
            'x' => $this->socialProfileForm['x'],
            'telegram' => $this->socialProfileForm['telegram'],
            'github' => $this->socialProfileForm['github'],
            'medium' => $this->socialProfileForm['medium'],
            'discord' => $this->socialProfileForm['discord'],
        ], [
            'address' => ['required', 'string', 'max:40', 'exists:nom_accounts,address'],
            //'message' => ['required', 'string', 'max:8'],
            'signature' => ['required', 'string'],

            'bio' => ['nullable', 'max:255'],
            'avatar' => ['nullable', 'url', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'x' => ['nullable', 'url', 'max:255'],
            'telegram' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'medium' => ['nullable', 'url', 'max:255'],
            'discord' => ['nullable', 'url', 'max:255'],
        ])->validateWithBag('verifyAddress');

        $userControlsAddress = auth()->user()?->whereRelation('verifiedAccounts', 'address', $this->address)->count();

        if (! $userControlsAddress) {
            $zenonSdk = app(ZenonSdk::class);
            $account = load_account($this->address);

            $validated = $zenonSdk->verifySignature(
                $account->decoded_public_key,
                $account->address,
                $this->message,
                $this->signature,
            );

            if (! $validated) {

                $this->setRandomMessage();
                $this->signature = '';

                throw ValidationException::withMessages([
                    'signature' => [__('Unable to verify profile ownership, please try again')],
                ]);
            }
        }

        $this->socialProfile->update($this->socialProfileForm);

        $this->dispatch('social-profile.updated');
    }

    private function setRandomMessage(): void
    {
        $this->message = app()->isProduction()
            ? Str::upper(Str::random(8))
            : 'TEST';
    }

    private function loadSocialProfile(): void
    {
        if ($this->socialProfile === null && $this->itemType) {

            $address = null;
            $type = null;
            $id = null;

            if ($this->itemType === 'address') {
                $type = Account::class;
                $id = $this->itemId;
                $address = $this->itemId;
            }

            if ($this->itemType === 'pillar') {
                $type = Pillar::class;
                $pillar = Pillar::firstWhere('slug', $this->itemId)?->load('owner');
                $id = $pillar?->id;
                $address = $pillar?->owner->address;
            }

            if ($this->itemType === 'token') {
                $type = Token::class;
                $token = Token::firstWhere('token_standard', $this->itemId)?->load('owner');
                $id = $token?->id;
                $address = $token?->owner->address;
            }

            $this->address = $address;
            $this->socialProfile = SocialProfile::findByProfileableType($type, $id);
            if ($this->socialProfile) {
                $this->socialProfileForm = $this->socialProfile?->toArray();
            }
        }
    }
}
