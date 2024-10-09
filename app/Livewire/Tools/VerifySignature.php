<?php

declare(strict_types=1);

namespace App\Livewire\Tools;

use App\Services\ZenonSdk;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class VerifySignature extends Component
{
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

    public function render(): View
    {
        return view('livewire.tools.verify-signature');
    }

    public function verifySignature(): void
    {
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
