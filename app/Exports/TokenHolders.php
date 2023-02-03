<?php

namespace App\Exports;

use App\Models\Nom\Token;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TokenHolders implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public Token $token;
    public ?string $search;
    public ?string $sort;
    public ?string $order;

    public function __construct(Token $token, ?string $search = null, ?string $sort = null, ?string $order = null)
    {
        $this->token = $token;
        $this->search = $search;
        $this->sort = $sort;
        $this->order = $order;
    }

    public function headings(): array
    {
        return [
            'Address',
            'Balance',
        ];
    }

    public function map($row): array
    {
        $token = Token::find($row->pivot->token_id);

        return [
            $row->address,
            float_number($token?->getDisplayAmount($row->pivot->balance)),
        ];
    }

    public function query()
    {
        $query = $this->token->holders()
            ->wherePivot('balance', '>', '0');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('address', 'LIKE', "%{$this->search}%")
                    ->orWhere('name', 'LIKE', "%{$this->search}%");
            });
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
