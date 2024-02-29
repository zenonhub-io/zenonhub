<?php

declare(strict_types=1);

namespace App\View\Components\DateTime;

use Carbon\Carbon as CarbonAlias;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Carbon extends Component
{
    public function __construct(
        public DateTimeInterface $date,
        public string $format = 'jS M Y h:i:s A',
        public bool $human = false,
        public int $parts = 2,
        public bool $syntax = false
    ) {
        $this->date = CarbonAlias::instance($date);
    }

    public function render() : View
    {
        return view('components.date-time.carbon');
    }
}
