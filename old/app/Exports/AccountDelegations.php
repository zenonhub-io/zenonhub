<?php

declare(strict_types=1);

namespace App\Exports;

use App\Domains\Nom\Models\Account;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountDelegations implements FromQuery, WithHeadings, WithMapping
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
            'Pillar',
            'Started',
            'Ended',
            'Duration (seconds)',
        ];
    }

    public function map($row): array
    {
        return [
            $row->pillar->name,
            $row->started_at->format('Y-m-d H:i:s'),
            $row->ended_at?->format('Y-m-d H:i:s'),
            $row->duration_in_seconds,
        ];
    }

    public function query()
    {
        $query = $this->account->delegations();

        if ($this->search) {
            $query->whereHas('pillar', function ($q) {
                $q->where('name', 'LIKE', "%{$this->search}%");
            });
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
