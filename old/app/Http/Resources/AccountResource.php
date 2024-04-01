<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'address' => $this->address,
            'public_key' => $this->public_key,
            'znn_balance' => $this->znn_balance,
            'qsr_balance' => $this->qsr_balance,
            'display_znn_balance' => $this->display_znn_balance,
            'display_qsr_balance' => $this->display_qsr_balance,
            'first_active_at' => $this->first_active_at,
            'last_active_at' => $this->latestBlock ? $this->latestBlock->created_at : null,
        ];
    }
}
