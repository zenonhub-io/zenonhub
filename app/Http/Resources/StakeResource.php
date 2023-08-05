<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StakeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'account' => $this->account->address,
            'token' => $this->token->name,
            'display_amount' => $this->display_amount,
            'amount' => $this->amount,
            'duration' => $this->duration,
            'hash' => $this->hash,
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
        ];
    }
}
