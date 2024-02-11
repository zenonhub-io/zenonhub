<?php

namespace App\Exports;

use App\Models\Nom\Pillar;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PillarHistory implements FromQuery, WithHeadings, WithMapping
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
            'Momentum reward',
            'Delegate reward',
            'Timestamp',
        ];
    }

    public function map($row): array
    {
        return [
            $row->momentum_rewards,
            $row->delegate_rewards,
            $row->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        $query = $this->pillar->history()
            ->where('is_reward_change', '1');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('momentum_rewards', $this->search)
                    ->orWhere('delegate_rewards', $this->search);
            });
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
