<?php

namespace App\Http\Livewire\Tools;

use App\Actions\PlasmaBot\Fuse;
use App\Models\Nom\Account;
use App\Models\PlasmaBotEntry;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class PlasmaBot extends Component
{
    use UsesSpamProtection;

    public ?bool $result = null;

    public ?string $address = null;

    public ?string $plasma = 'low';

    public ?string $expires = null;

    public ?string $message = null;

    public HoneypotData $extraFields;

    protected function rules()
    {
        return [
            'address' => [
                'required',
                'string',
                'size:40',
            ],
            'plasma' => [
                'required',
                'in:low,medium,high',
            ],
        ];
    }

    public function mount()
    {
        $this->extraFields = new HoneypotData();
    }

    public function render()
    {
        $account = Account::findByAddress(config('plasma-bot.address'));
        $totalQsrAvailable = $account->qsr_balance;
        $fusedQsr = $account->fusions()->isActive()->sum('amount');
        $percentageAvailable = ($fusedQsr / $totalQsrAvailable) * 100;
        $nextExpiring = PlasmaBotEntry::orderBy('expires_at', 'desc')->first();

        return view('livewire.tools.plasma-bot', [
            'account' => $account,
            'percentageAvailable' => $percentageAvailable,
            'percentageUsed' => (100 - $percentageAvailable),
            'nextExpiring' => $nextExpiring,
        ]);
    }

    public function submit()
    {
        $this->protectAgainstSpam();

        $data = $this->validate();
        $this->result = false;
        $this->message = false;

        $existing = PlasmaBotEntry::isActive()
            ->whereAddress($data['address'])
            ->first();

        if ($existing) {
            $duration = now()->timestamp - $existing->expires_at->timestamp;
            $remainingTime = now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
            $this->message = 'This address already has plasma fused for '.$remainingTime;

            return;
        }

        $rateLimitKey = 'plasma-bot-fuse:'.request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->result = false;
            $this->message = "Too many requests, please try again in {$seconds} seconds";

            return;
        }

        RateLimiter::hit($rateLimitKey);

        $plasma = match ($data['plasma']) {
            'high' => 120,
            'medium' => 80,
            default => 20,
        };

        $expires = match ($plasma) {
            120 => now()->addHours(12),
            80 => now()->addDay(),
            default => now()->addDays(2),
        };

        $duration = now()->timestamp - $expires->timestamp;
        $this->expires = now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
        $this->result = (new Fuse($data['address'], $plasma, $expires))->execute();
    }
}
