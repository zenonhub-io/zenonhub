<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Api\ApiController;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TransactionStatsController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $start = now()->subDays(15)->startOfDay()->format('Y-m-d H:i:s');
        $end = now()->subDay()->endOfDay()->format('Y-m-d H:i:s');
        $cacheKey = 'tx-stats-' . $start . '-' . $end;

        $response = Cache::rememberForever($cacheKey, function () use ($start, $end) {
            $results = [];
            $contractMethods = ContractMethod::get();
            $contractMethodIds = $contractMethods->pluck('id');
            $contractTxs = AccountBlock::selectRaw('DATE(created_at) as date, COUNT(*) as totalTx, nom_contracts.name as contract_name, nom_contract_methods.name as contract_method_name')
                ->leftJoin('nom_contract_methods', 'nom_contract_methods.id', '=', 'nom_account_blocks.contract_method_id')
                ->leftJoin('nom_contracts', 'nom_contracts.id', '=', 'nom_contract_methods.contract_id')
                ->whereIn('contract_method_id', $contractMethodIds)
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date', 'contract_method_id')
                ->get();

            $contractTxs->each(function ($statistic) use (&$results) {

                if (! $statistic->totalTx) {
                    return;
                }

                $arrayKey = $statistic->contract_name . '.' . $statistic->contract_method_name;
                $results[$statistic->date]['contracts'][$arrayKey] = $statistic->totalTx;
            });

            $normalTxs = AccountBlock::selectRaw('DATE(created_at) as date, COUNT(*) as totalTx, block_type')
                ->whereNull('contract_method_id')
                ->whereBetween('created_at', [$start, $end])
                ->groupBy('date', 'block_type')
                ->get();

            $normalTxs->each(function ($statistic) use (&$results) {

                if (! $statistic->totalTx) {
                    return;
                }

                $arrayKey = 'Genesis';

                if ($statistic->block_type === 2) {
                    $arrayKey = 'Send';
                }

                if ($statistic->block_type === 3) {
                    $arrayKey = 'Receive';
                }

                if ($statistic->block_type === 4) {
                    $arrayKey = 'ContractSend';
                }

                if ($statistic->block_type === 5) {
                    $arrayKey = 'ContractReceive';
                }

                $results[$statistic->date][$arrayKey] = $statistic->totalTx;
            });

            return $results;
        });

        return $this->success($response);
    }
}
