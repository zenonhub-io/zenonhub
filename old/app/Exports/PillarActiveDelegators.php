<?php

declare(strict_types=1);

namespace App\Exports;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Pillar;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PillarActiveDelegators implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public Pillar $pillar;

    public ?string $search;

    public ?string $sort;

    public ?string $order;

    public function __construct(Pillar $pillar, ?string $search = null, ?string $sort = null, ?string $order = null)
    {
        $this->pillar = $pillar;
        $this->search = $search;
        $this->sort = $sort;
        $this->order = $order;
    }

    public function headings(): array
    {
        return [
            'Address',
            'Started',
            'Duration',
            'Weight',
        ];
    }

    public function map($row): array
    {
        return [
            $row->account->address,
            $row->started_at->format('Y-m-d H:i:s'),
            $row->started_at->diffInSeconds(now()),
            float_number($row->display_weight),
        ];
    }

    public function query()
    {
        $query = $this->pillar->delegators()
            ->whereHas('account', function ($q) {
                $q->where('znn_balance', '>', '0');
            })
            ->whereNull('ended_at');

        if ($this->search) {
            $query->whereHas('account', function ($q) {
                $q->where('address', 'LIKE', "%{$this->search}%");
                $q->orWhere('name', 'LIKE', "%{$this->search}%");
            });
        }

        if ($this->sort === 'weight') {
            $query->orderBy(
                Account::select('znn_balance')->whereColumn('nom_accounts.id', 'nom_pillar_delegators.account_id'),
                $this->order
            );
        } else {
            $query->orderBy($this->sort, $this->order);
        }

        return $query;
    }
}
