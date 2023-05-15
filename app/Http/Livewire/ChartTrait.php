<?php

namespace App\Http\Livewire;

use Illuminate\Support\Collection;

trait ChartTrait
{
    public string $type = 'line';

    public array $dataset = [];

    public array $labels = [];

    public array $options = [
        'responsive' => true,
        'scales' => [
            'x' => [
                'grid' => [
                    'display' => false,
                ],
            ],
            'y' => [
                'grid' => [
                    'display' => false,
                ],
            ],
        ],
    ];

    public string $uuid;

    private Collection $data;
}
