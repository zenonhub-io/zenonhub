<?php

declare(strict_types=1);

namespace App\Exports;

use App\Domains\Nom\Models\Account;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AccountTokens implements FromQuery, WithHeadings, WithMapping
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
            'Token',
            'Balance',
        ];
    }

    public function map($row): array
    {
        return [
            $row->name,
            float_number($row->getDisplayAmount($row->pivot->balance)),
        ];
    }

    public function query()
    {
        $query = $this->account->balances()
            ->wherePivot('balance', '>', '0');

        if ($this->search) {
            $query->where('name', $this->search);
        }

        if ($this->sort === 'default') {
            if ($this->order === 'desc') {
                $query->orderByRaw('(token_id = 6) desc')
                    ->orderByRaw('(token_id = 3) desc')
                    ->orderBy('balance', 'DESC');
            } else {
                $query->orderByRaw('(token_id = 6) desc')
                    ->orderByRaw('(token_id = 3) desc')
                    ->orderBy('balance', 'ASC');
            }
        } else {
            $query->orderBy($this->sort, $this->order);
        }

        return $query;
    }
}
