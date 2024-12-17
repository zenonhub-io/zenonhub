<?php

declare(strict_types=1);

namespace App\Livewire\Tools;

use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;
use Throwable;

class VerifySignature extends Component
{
    use UsesSpamProtection;

    public ?bool $result = null;

    #[Validate([
        'verifySignatureForm.address' => [
            'required',
            'string',
        ],
        'verifySignatureForm.publicKey' => [
            'required',
            'string',
        ],
        'verifySignatureForm.message' => [
            'required',
            'string',
        ],
        'verifySignatureForm.signature' => [
            'required',
            'string',
        ],
    ])]
    public array $verifySignatureForm = [
        'address' => '',
        'publicKey' => '',
        'message' => '',
        'signature' => '',
    ];

    public HoneypotData $extraFields;

    public function mount(): void
    {
        $this->extraFields = new HoneypotData;
    }

    public function render(): View
    {
        return view('livewire.tools.verify-signature');
    }

    public function verifySignature(): void
    {
        $this->protectAgainstSpam();
        $this->validate();

        $zenonSdk = app(ZenonSdk::class);

        try {
            $this->result = $zenonSdk->verifySignature(
                $this->verifySignatureForm['publicKey'],
                $this->verifySignatureForm['address'],
                $this->verifySignatureForm['message'],
                $this->verifySignatureForm['signature'],
            );
        } catch (Throwable $throwable) {
            throw ValidationException::withMessages([
                'verifySignatureForm.signature' => [__('Invalid signature or address')],
            ]);
        }
    }
}
