<?php

declare(strict_types=1);

namespace App\Exports;

use App\Domains\Nom\Models\AccountBlock;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BlockDescendants implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public AccountBlock $accountBlock;

    public ?string $search;

    public ?string $sort;

    public ?string $order;

    public function __construct(AccountBlock $accountBlock, ?string $search = null, ?string $sort = null, ?string $order = null)
    {
        $this->accountBlock = $accountBlock;
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
            'Amount',
            'Token',
            'Timestamp',
        ];
    }

    public function map($row): array
    {
        return [
            $row->height,
            $row->hash,
            $row->account?->address,
            $row->toAccount?->address,
            $row->display_type,
            float_number($row->display_amount),
            $row->token?->name,
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        $query = $this->accountBlock->descendants();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('height', $this->search)
                    ->orWhere('hash', $this->search)
                    ->orWhereHas('token', fn ($q2) => $q2->where('name', $this->search))
                    ->orWhereHas('account', fn ($q3) => $q3->where('address', $this->search))
                    ->orWhereHas('toAccount', fn ($q4) => $q4->where('address', $this->search));
            });
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
