<?php

namespace App\Exports;

use App\Models\Nom\Token;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TokenMints implements FromQuery, WithHeadings, WithMapping
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
            'Issuer',
            'Receiver',
            'Amount',
            'Transaction',
            'Timestamp',
        ];
    }

    public function map($row): array
    {
        $token = Token::find($row->token_id);

        return [
            $row->issuer->address,
            $row->receiver->address,
            float_number($token?->getDisplayAmount($row->amount)),
            $row->account_block->hash,
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function query()
    {
        $query = $this->token->mints();

        if ($this->search) {
            $query->whereHas('issuer', function ($q) {
                $q->where('address', 'LIKE', "%{$this->search}%");
                $q->orWhere('name', 'LIKE', "%{$this->search}%");
            })->orWhereHas('receiver', function ($q) {
                $q->where('address', 'LIKE', "%{$this->search}%");
                $q->orWhere('name', 'LIKE', "%{$this->search}%");
            });
        }

        $query->orderBy($this->sort, $this->order);

        return $query;
    }
}
