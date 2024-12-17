<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Nom\Account;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountRewards implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public Account $account;

    public ?string $search;

    public ?string $sort;

    public ?string $order;

    public function __construct(Account $account, ?string $search = null, ?string $sort = null, ?string $order = null)
    {
        $this->account = $account;
        $this->search = $search;
        $this->sort = $sort;
        $this->order = $order;
    }

    public function headings(): array
    {
        return [
            'Type',
            'Token',
            'Amount',
            'Timestamp',
        ];
    }

    public function map($row): array
    {
        return [
            $row->display_type,
            $row->token?->name,
            float_number($row->display_amount),
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        $query = $this->account->rewards();

        if ($this->search) {
            $searchTerm = strtolower($this->search);
            if ($searchTerm === 'delegate') {
                $query->where('type', '1');
            } elseif ($searchTerm === 'stake') {
                $query->where('type', '2');
            } elseif ($searchTerm === 'pillar') {
                $query->where('type', '3');
            } elseif ($searchTerm === 'sentinel') {
                $query->where('type', '4');
            } elseif ($searchTerm === 'liquidity') {
                $query->where('type', '5');
            }
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
