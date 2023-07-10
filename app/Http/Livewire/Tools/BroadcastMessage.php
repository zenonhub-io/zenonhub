<?php

namespace App\Http\Livewire\Tools;

use App\Models\Nom\Account;
use App\Models\Nom\Pillar;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Livewire\Component;

class BroadcastMessage extends Component
{
    public ?string $address = null;

    public ?string $public_key = null;

    public ?string $message = null;

    public ?string $signature = null;

    public ?string $title = null;

    public ?string $post = null;

    public ?bool $result = null;

    public ?string $error = null;

    public ?\Illuminate\Database\Eloquent\Collection $pillars = null;

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
            'title' => [
                'required',
                'string',
            ],
            'post' => [
                'required',
                'string',
            ],
        ];
    }

    public function mount()
    {
        $this->message = Str::upper(Str::random(8));
        $this->pillars = Pillar::isActive()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.tools.broadcast-message');
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
        $account = Account::findByAddress($data['address']);
        $zenonService = App::make('zenon.services');
        $validated = $zenonService->verifySignature($data['public_key'], $data['address'], $data['message'], $data['signature']);

        if (! $validated) {
            $this->error = 'Invalid signature';
            $this->result = false;

            return;
        }

        if (! $account?->pillar) {
            $this->error = 'Address is not a pillar, only pillars can broadcast messages';
            $this->result = false;

            return;
        }

        $this->result = $zenonService->broadcastSignedMessage($account, $data['title'], $data['post'], $data['message'], $data['signature']);
    }
}
