<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tools;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Pillar;
use App\Services\ZenonSdk;
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

    public function mount()
    {
        $this->message = Str::upper(Str::random(8));
        $this->pillars = Pillar::whereActive()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.tools.broadcast-message');
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'address') {
            $accountCheck = Account::firstWhere('address', $this->address);
            if ($accountCheck) {
                $this->public_key = $accountCheck->decoded_public_key;
            }
        }

        $this->validateOnly($propertyName);
    }

    public function submit()
    {
        $data = $this->validate();
        $account = Account::firstWhere('address', $data['address']);
        $validated = ZenonSdk::verifySignature($data['public_key'], $data['address'], $data['message'], $data['signature']);

        if (! $validated) {
            $this->error = 'Invalid signature';
            $this->result = false;

            return;
        }

        if (! $account || ! $account?->pillar) {
            $this->error = 'Address is not a pillar, only pillars can broadcast messages';
            $this->result = false;

            return;
        }

        $this->result = $this->broadcastSignedMessage($account, $data['title'], $data['post'], $data['message'], $data['signature']);
    }

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

    private function broadcastSignedMessage(Account $account, string $title, string $post, string $message, string $signature): bool
    {
        $discourseApi = App::make('discourse.api');
        $pillar = Pillar::where('owner_id', $account->id)->first();
        $pillarMessage = $pillar->messages()->create([
            'title' => $title,
            'post' => $post,
            'message' => $message,
            'signature' => $signature,
            'is_public' => true,
            'created_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $verificationLink = route('tools.verify-signature', [
            'address' => $account->address,
            'public_key' => $account->public_key,
            'message' => $message,
            'signature' => $signature,
        ]);
        $messageMarkdown = "{$post}
---
```
# Proof of Pillar
Pillar: {$pillar->name}
Address: {$account->address}
Public Key: {$account->public_key}
Message: {$message}
Signature: {$signature}
```
[Verify this message]({$verificationLink})";

        $forumPost = $discourseApi->createTopic($title, $messageMarkdown, 20, 'system', 0, now());

        if ($forumPost && $forumPost->http_code === 200) {
            $pillarMessage->is_broadcast = true;
            $pillarMessage->save();

            return true;
        }

        return false;
    }
}
