<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\PlasmaBot;

use App\Http\Controllers\Api\ApiController;
use App\Models\PlasmaBotEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FuseExpirationController extends ApiController
{
    public function __invoke(Request $request, string $address): JsonResponse
    {
        $validator = Validator::make([
            'address' => $address,
        ], [
            'address' => 'required|string|size:40',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $fuse = PlasmaBotEntry::whereRelation('account', 'address', $address)
            ->whereConfirmed()
            ->first();

        if (! $fuse) {
            return $this->error(
                'Address not found',
                'The supplied address has not used the plasma bot service.'
            );
        }

        $expirationDate = $fuse->expires_at;

        if (! $expirationDate) {
            $account = load_account($address);
            if ($account && $account->latest_block) {
                $account->latest_block->created_at->addDays(30);
            }
        }

        if (! $expirationDate) {
            $expirationDate = now()->addDays(30);
        }

        return $this->success($expirationDate->format('Y-m-d H:i:s'));
    }
}
