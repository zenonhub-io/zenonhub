<?php

declare(strict_types=1);

namespace App\Livewire\Utilities;

use App\Models\Nom\Momentum;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CurrentHeight extends Component
{
    public function render(): View
    {
        $latestMomentum = Momentum::getFrontier();
        $cutoff = now()->subMinutes(5);
        $height = $latestMomentum->height;
        $stillProducing = $latestMomentum->created_at->greaterThanOrEqualTo($cutoff);
        $message = $stillProducing
            ? __('Explorer synced')
            : __('Last momentum synced ') . $latestMomentum->created_at->diffForHumans(['parts' => 2]);

        return view('livewire.utilities.current-height', [
            'height' => $height,
            'status' => $stillProducing,
            'created_at' => $latestMomentum->created_at,
            'message' => $message,
        ]);
    }
}
