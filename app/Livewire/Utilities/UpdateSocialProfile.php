<?php

declare(strict_types=1);

namespace App\Livewire\Utilities;

use App\Models\Nom\Pillar;
use App\Models\Nom\Token;
use App\Models\SocialProfile;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UpdateSocialProfile extends Component
{
    public ?string $title = null;

    public ?string $itemType = null;

    public ?string $itemId = null;

    public ?SocialProfile $socialProfile = null;

    public string $address = '';

    public string $message = '';

    public string $signature = '';

    public bool $hasUserVerifiedAddress = false;

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

    public function render(): View
    {
        return view('livewire.utilities.update-social-profile');
    }

    public function mount(): void
    {
        $this->loadSocialProfile();
        $this->checkUserHasVerifiedAddress();
        $this->setRandomMessage();
    }

    public function saveProfile(): void
    {
        $this->resetErrorBag();

        $rules = [
            'address' => ['required', 'string', 'max:40', 'exists:nom_accounts,address'],
            'bio' => ['nullable', 'max:255'],
            'avatar' => ['nullable', 'url', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'x' => ['nullable', 'url', 'max:255'],
            'telegram' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'medium' => ['nullable', 'url', 'max:255'],
            'discord' => ['nullable', 'url', 'max:255'],
        ];

        if (! $this->hasUserVerifiedAddress) {
            $rules += [
                'message' => ['required', 'string', 'max:8'],
                'signature' => ['required', 'string'],
            ];
        }

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
        ], $rules)->validateWithBag('verifyAddress');

        if (! $this->hasUserVerifiedAddress) {
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

        $this->setRandomMessage();
        $this->dispatch('social-profile.updated');
    }

    private function setRandomMessage(): void
    {
        $this->message = app()->isProduction()
            ? Str::upper(Str::random(8))
            : 'TEST';
    }

    private function checkUserHasVerifiedAddress(): void
    {
        $userControlsAddress = auth()->user()?->whereRelation('verifiedAccounts', 'address', $this->address)->count();

        if ($userControlsAddress > 0) {
            $this->hasUserVerifiedAddress = true;
        }
    }

    private function loadSocialProfile(): void
    {
        if ($this->socialProfile === null && $this->itemType) {

            $model = null;
            $address = null;

            if ($this->itemType === 'address') {
                $account = $model = load_account($this->itemId);
                $address = $account->address;
            }

            if ($this->itemType === 'pillar') {
                $pillar = $model = Pillar::firstWhere('slug', $this->itemId)?->load('owner');
                $address = $pillar?->owner->address;
            }

            if ($this->itemType === 'token') {
                $token = $model = Token::firstWhere('token_standard', $this->itemId)?->load('owner');
                $address = $token?->owner->address;
            }

            $socialProfile = $model?->socialProfile;

            if (! $socialProfile) {
                $socialProfile = new SocialProfile;
                $model->socialProfile()->save($socialProfile);
            }

            $this->address = $address;
            $this->socialProfile = $socialProfile;
            $this->socialProfileForm = $this->socialProfile->toArray();
        }
    }
}
