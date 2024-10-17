<?php

declare(strict_types=1);

namespace App\Livewire\Tools;

use App\Models\PlasmaBotEntry;
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

    public HoneypotData $extraFields;

    public function mount()
    {
        $this->extraFields = new HoneypotData;
    }

    public function render(): View
    {
        $account = load_account(config('services.plasma-bot.address'));
        $totalQsrAvailable = $account->qsr_balance;
        $fusedQsr = $account->fusions()->whereActive()->sum('amount');
        $percentageAvailable = ($fusedQsr / $totalQsrAvailable) * 100;
        $nextExpiring = PlasmaBotEntry::orderBy('expires_at', 'desc')->first();

        return view('livewire.tools.plasma-bot', [
            'account' => $account,
            'percentageAvailable' => $percentageAvailable,
            'percentageUsed' => (100 - $percentageAvailable),
            'nextExpiring' => $nextExpiring,
        ]);
    }

    public function fusePlasma()
    {
        $this->protectAgainstSpam();
    }
}
