<?php

declare(strict_types=1);

namespace App\Exports;

use App\Domains\Nom\Models\Token;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class TokenMints extends DefaultValueBinder implements FromQuery, WithCustomValueBinder, WithHeadings, WithMapping
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
            $token?->getDisplayAmount($row->amount),
            $row->accountBlock->hash,
            $row->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function bindValue($cell, $value)
    {
        if (is_numeric($value)) {
            $cell->setValueExplicit($value);

            return true;
        }

        return parent::bindValue($cell, $value);
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
