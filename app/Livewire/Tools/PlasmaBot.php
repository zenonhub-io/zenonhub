<?php

declare(strict_types=1);

namespace App\Livewire\Tools;

use App\Actions\PlasmaBot\Fuse;
use App\Exceptions\PlasmaBotException;
use App\Models\PlasmaBotEntry;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Honeypot\Http\Livewire\Concerns\HoneypotData;
use Spatie\Honeypot\Http\Livewire\Concerns\UsesSpamProtection;

class PlasmaBot extends Component
{
    use UsesSpamProtection;

    #[Validate([
        'fuseForm.address' => [
            'required',
            'string',
        ],
        'fuseForm.amount' => [
            'required',
            'string',
        ],
    ])]
    public array $fuseForm = [
        'address' => '',
        'amount' => 'low',
    ];

    public string $plasmaLevelInfo;

    public ?string $message;

    public ?bool $result;

    public ?string $expires;

    public HoneypotData $extraFields;

    public function mount()
    {
        $this->setPlasmaLevelInfo();
        $this->extraFields = new HoneypotData;
    }

    public function render(): View
    {
        return view('livewire.tools.plasma-bot', [
            'enabled' => $this->isBotEnabled(),
            'stats' => $this->getBotData(),
        ]);
    }

    public function fusePlasma(): void
    {
        $this->protectAgainstSpam();
        $this->validate();
        $this->result = null;
        $this->message = null;

        $existing = PlasmaBotEntry::whereConfirmed()
            ->whereRelation('account', 'address', $this->fuseForm['address'])
            ->first();

        if ($existing) {
            $duration = now()->timestamp - $existing->expires_at->timestamp;
            $remainingTime = now()->subSeconds($duration)->diffForHumans(['parts' => 2], true);
            $this->message = 'This address already has plasma fused for ' . $remainingTime;

            return;
        }

        $rateLimitKey = 'plasma-bot-fuse:' . request()->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 1) && app()->isProduction()) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $this->result = false;
            $this->message = "Too many requests, please try again in {$seconds} seconds";

            return;
        }

        RateLimiter::hit($rateLimitKey);

        $plasma = match ($this->fuseForm['amount']) {
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

        try {
            Fuse::run($this->fuseForm['address'], $plasma, $expires);
            $this->result = true;
        } catch (PlasmaBotException $exception) {
            Log::error('Plasma bot error: ' . $exception->getMessage());
            $this->result = false;
        }
    }

    public function setPlasmaLevelInfo(): void
    {
        $this->plasmaLevelInfo = match ($this->fuseForm['amount']) {
            'low' => __('20 QSR fused for 48 hours'),
            'medium' => __('80 QSR fused for 24 hours'),
            'high' => __('120 QSR fused for 12 hours'),
        };
    }

    private function isBotEnabled(): bool
    {
        return config('services.plasma-bot.enabled');
    }

    private function getBotData(): array
    {
        if (! $this->isBotEnabled()) {
            return [];
        }

        $account = load_account(config('services.plasma-bot.address'));
        $totalQsrAvailable = $account->qsr_balance;
        $fusedQsr = $account->fusions()->whereActive()->sum('amount');
        $percentageUsed = ($fusedQsr / $totalQsrAvailable) * 100;
        $nextExpiring = PlasmaBotEntry::whereNotNull('expires_at')->orderBy('expires_at', 'desc')->first();

        return [
            'account' => $account,
            'percentageAvailable' => 100 - $percentageUsed,
            'percentageUsed' => $percentageUsed,
            'nextExpiring' => $nextExpiring,
        ];
    }
}
