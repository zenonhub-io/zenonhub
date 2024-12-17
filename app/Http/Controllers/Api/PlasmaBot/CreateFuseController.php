<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\PlasmaBot;

use App\Actions\PlasmaBot\Fuse;
use App\Exceptions\PlasmaBotException;
use App\Http\Controllers\Api\ApiController;
use App\Models\PlasmaBotEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateFuseController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string|size:40',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $fuseAmount = 100;
        $address = $request->input('address');
        $plasmaBotAccount = load_account(config('services.plasma-bot.address'));

        if ($plasmaBotAccount->qsr_balance < $fuseAmount) {
            return $this->error(
                'Unable to fuse QSR',
                'Not enough QSR available in the bot, try again later',
                400
            );
        }

        $existingFuse = PlasmaBotEntry::whereRelation('account', 'address', $address)
            ->whereConfirmed()
            ->exists();

        if (! $existingFuse) {
            try {
                Fuse::run($address, $fuseAmount);
            } catch (PlasmaBotException $exception) {
                return $this->error(
                    'Error fusing QSR',
                    'An error occurred while fusing QSR, please try again',
                    400
                );
            }
        }

        return $this->success('Success');
    }
}
