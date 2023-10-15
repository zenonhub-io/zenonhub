<?php

namespace App\Http\Livewire\Tools;

use App\Models\Nom\Account;
use App\Services\Zenon;
use Illuminate\Http\Request;
use Livewire\Component;

class VerifySignature extends Component
{
    public ?string $address = null;

    public ?string $public_key = null;

    public ?string $message = null;

    public ?string $signature = null;

    public ?bool $result = null;

    protected function rules()
    {
        return [
            'address' => [
                'required',
                'string',
                'exists:nom_accounts,address',
            ],
            'public_key' => [
                'required',
                'string',
            ],
            'message' => [
                'required',
                'string',
            ],
            'signature' => [
                'required',
                'string',
            ],
        ];
    }

    public function mount(Request $request)
    {
        $this->address = $request->get('address');
        $this->public_key = $request->get('public_key');
        $this->message = $request->get('message');
        $this->signature = $request->get('signature');
    }

    public function render()
    {
        return view('livewire.tools.verify-signature');
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'address') {
            $accountCheck = Account::findByAddress($this->address);
            if ($accountCheck) {
                $this->public_key = $accountCheck->decoded_public_key;
            }
        }

        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $data = $this->validate();
        $this->result = Zenon::verifySignature($data['public_key'], $data['address'], $data['message'], $data['signature']);
    }
}
