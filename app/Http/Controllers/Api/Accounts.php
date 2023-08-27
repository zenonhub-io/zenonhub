<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Resources\AccountCollection;
use App\Http\Resources\AccountResource;
use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Validator;

class Accounts extends ApiController
{
    public function get(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $results = $this->filterQuery(Account::query(), $request)
            ->paginate($request->input('per_page', 100));

        return $this->success(new AccountCollection($results));
    }

    public function find(Request $request, string $address)
    {
        $account = Account::findByAddress($address);

        if (! $account) {
            return $this->error('Not found');
        }

        return $this->success(new AccountResource($account));
    }

    private function filterQuery(Builder $query, Request $request): Builder
    {

        return $query;
    }
}
