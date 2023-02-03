<?php

namespace App\Exports;

use App\Models\Nom\Account;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountStakes implements FromQuery, WithHeadings, WithMapping
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
            'Amount',
            'Starts',
            'Ends',
            'Duration (seconds)',
        ];
    }

    public function map($row): array
    {
        return [
            float_number($row->display_amount),
            $row->started_at->format('Y-m-d H:i:s'),
            $row->end_date->format('Y-m-d H:i:s'),
            $row->duration,
        ];
    }

    public function query()
    {
        $query = $this->account->stakes()
            ->whereNull('ended_at');

        if ($this->search) {
            $query->where('amount', (is_numeric($this->search) ? $this->search * 100000000 : '0'));
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
