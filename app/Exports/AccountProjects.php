<?php

namespace App\Exports;

use App\Models\Nom\Account;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountProjects implements FromQuery, WithHeadings, WithMapping
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
            'Title',
            'Hash',
            'ZNN',
            'QSR',
            'Status',
            'Timestamp',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            $row->hash,
            float_number($row->display_znn_funds_needed),
            float_number($row->display_qsr_funds_needed),
            $row->display_status,
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        $query = $this->account->projects();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%");
                $q->orWhere('hash', 'LIKE', "%{$this->search}%");
            });
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
