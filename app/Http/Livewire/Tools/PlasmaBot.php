<?php

namespace App\Http\Livewire\Tools;

use App\Actions\PlasmaBot\Fuse;
use App\Models\Nom\Account;
use Livewire\Component;

class PlasmaBot extends Component
{
    public ?bool $result = null;

    public ?string $address = null;

    public ?string $plasma = null;

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

    public function render()
    {
        $account = Account::findByAddress(config('plasma-bot.address'));
        $totalQsrAvailable = 100 * (100000000);
        $fusedQsr = $account->fusions->sum('amount');
        $percentageAvailable = ($fusedQsr / $totalQsrAvailable) * 100;

        return view('livewire.tools.plasma-bot', [
            'account' => $account,
            'percentageAvailable' => $percentageAvailable,
            'percentageUsed' => (100 - $percentageAvailable),
        ]);
    }

    public function submit()
    {
        $data = $this->validate();

        $plasma = match ($data['plasma']) {
            'medium' => 80,
            'high' => 120,
            default => 40,
        };

        $this->result = (new Fuse($data['address'], $plasma))->execute();
    }
}
