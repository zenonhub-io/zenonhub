<?php

namespace App\Exports;

use App\Models\Nom\Momentum;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MomentumBlocks implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public Momentum $momentum;
    public ?string $search;
    public ?string $sort;
    public ?string $order;

    public function __construct(Momentum $momentum, ?string $search = null, ?string $sort = null, ?string $order = null)
    {
        $this->momentum = $momentum;
        $this->search = $search;
        $this->sort = $sort;
        $this->order = $order;
    }

    public function headings(): array
    {
        return [
            'Height',
            'Hash',
            'From',
            'To',
            'Type',
            'Method',
            'Token',
            'Amount',
            'Timestamp',
        ];
    }

    public function map($row): array
    {
        return [
            $row->height,
            $row->hash,
            $row->account?->address,
            $row->to_account?->address,
            $row->display_type,
            $row->contract_method?->name,
            $row->token?->name,
            float_number($row->display_amount),
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        $query = $this->momentum->account_blocks();;

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('height', $this->search);
                $q->orWhere('hash', $this->search);
                $q->orWhereHas('token', fn($q2) => $q2->where('name', $this->search));
                $q->orWhereHas('account', function ($q2) {
                    $q2->where('address', 'LIKE', "%{$this->search}%")
                        ->orWhere('name', 'LIKE', "%{$this->search}%");
                });
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
