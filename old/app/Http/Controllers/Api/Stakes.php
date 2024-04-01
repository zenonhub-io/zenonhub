<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domains\Nom\Models\Stake;
use App\Http\Resources\StakeCollection;
use App\Http\Resources\StakeResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Validator;

class Stakes extends ApiController
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
            'token' => 'sometimes|exists:nom_tokens,token_standard',
            'state' => 'sometimes|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $results = $this->filterQuery(Stake::query(), $request)
            ->paginate($request->input('per_page', 100));

        return $this->success(new StakeCollection($results));
    }

    public function find(Request $request, string $hash)
    {
        $stake = Stake::findBy('hash', $hash);

        if (! $stake) {
            return $this->error('Not found');
        }

        return $this->success(new StakeResource($stake));
    }

    private function filterQuery(Builder $query, Request $request): Builder
    {
        if ($request->input('token')) {
            $query->whereHas('token', fn ($q) => $q->where('token_standard', $request->input('token')));
        }

        if ($state = $request->input('state')) {
            if ($state === 'active') {
                $query->isActive();
            }

            if ($state === 'ended') {
                $query->isEnded();
            }
        }

        return $query;
    }
}
