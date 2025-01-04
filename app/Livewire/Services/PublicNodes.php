<?php

declare(strict_types=1);

namespace App\Livewire\Services;

use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class PublicNodes extends Component
{
    public string $tab = 'info';

    public mixed $data = [];

    public function render(): View
    {
        try {
            $this->data = match ($this->tab) {
                'info' => $this->loadInfoData(),
                'sync' => $this->loadSyncData(),
                'network' => $this->loadNetworkData(),
            };
        } catch (Throwable $th) {
            $this->data = [
                __('Unavailable, try again soon'),
            ];
        }

        return view('livewire.services.public-nodes');
    }

    #[On('show-tab')]
    public function changeTab($tab): void
    {
        $this->tab = $tab;
    }

    private function loadInfoData(): object
    {
        return app(ZenonSdk::class)->getProcessInfo();
    }

    private function loadSyncData(): object
    {
        return app(ZenonSdk::class)->getSyncInfo();
    }

    private function loadNetworkData(): object
    {
        return app(ZenonSdk::class)->getNetworkInfo();
    }
}
