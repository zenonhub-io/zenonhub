<?php

declare(strict_types=1);

namespace App\View\Components\DateTime;

use DateInterval;
use DateTimeInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Countdown extends Component
{
    public string $id;

    public DateTimeInterface $expires;

    public function __construct(DateTimeInterface $expires)
    {
        $this->id = 'timer-' . Str::random(6);
        $this->expires = $expires;
    }

    public function render(): View
    {
        return view('components.date-time.countdown');
    }

    public function days(): string
    {
        return sprintf('%02d', $this->difference()->d);
    }

    public function hours(): string
    {
        return sprintf('%02d', $this->difference()->h);
    }

    public function minutes(): string
    {
        return sprintf('%02d', $this->difference()->i);
    }

    public function seconds(): string
    {
        return sprintf('%02d', $this->difference()->s);
    }

    public function difference(): DateInterval
    {
        return $this->expires->diff(now());
    }
}
