<?php

namespace App\Exports;

use App\Models\Nom\Account;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountPlasma implements FromQuery, WithHeadings, WithMapping
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
            'Beneficiary',
            'Sender',
            'Amount',
            'Timestamp',
        ];
    }

    public function map($row): array
    {
        return [
            $row->to_account->address,
            $row->from_account->address,
            float_number($row->display_amount),
            $row->started_at->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        $query = $this->account->plasma()
            ->whereNull('ended_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->orWhereHas('to_account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
            });
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
