<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Exceptions\LivewireValidationException;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

trait DateRangePickerTrait
{
    public string $timeframe;

    public Carbon $startDate;

    public Carbon $endDate;

    private Collection $dateRange;

    private array $availableTimeframes = [
        'd',
        '7d',
        '30d',
        '60d',
        '90d',
        'w',
        'm',
        'y',
    ];

    public function nextDate(): void
    {
        $this->endDate = match ($this->timeframe) {
            'd' => $this->endDate->copy()->addDay(),
            '7d' => $this->endDate->copy()->addDays(7),
            '30d' => $this->endDate->copy()->addDays(30),
            '60d' => $this->endDate->copy()->addDays(60),
            '90d' => $this->endDate->copy()->addDays(90),
            'w' => $this->endDate->copy()->addWeek(),
            'm' => $this->endDate->copy()->addMonthWithOverflow(),
            'y' => $this->endDate->copy()->addYear(),
        };

        $this->setDateRange();
    }

    public function previousDate(): void
    {
        $this->endDate = match ($this->timeframe) {
            'd' => $this->endDate->copy()->subDay(),
            '7d' => $this->endDate->copy()->subDays(7),
            '30d' => $this->endDate->copy()->subDays(30),
            '60d' => $this->endDate->copy()->subDays(60),
            '90d' => $this->endDate->copy()->subDays(90),
            'w' => $this->endDate->copy()->subWeek(),
            'm' => $this->endDate->copy()->subMonthWithoutOverflow(),
            'y' => $this->endDate->copy()->subYear(),
        };

        $this->setDateRange();
    }

    public function getPeriodStart(Carbon $date): Carbon
    {
        return match ($this->timeframe) {
            'd' => $date->copy()->startOfHour(),
            'y' => $date->copy()->startOfMonth(),
            default => $date->copy()->startOfDay(),
        };
    }

    public function getPeriodEnd(Carbon $date): Carbon
    {
        return match ($this->timeframe) {
            'd' => $date->copy()->endOfHour(),
            'y' => $date->copy()->endOfMonth(),
            default => $date->copy()->endOfDay(),
        };
    }

    private function setDateRange(): void
    {
        $this->endDate = (match ($this->timeframe) {
            'w' => $this->endDate->endOfWeek(CarbonInterface::SUNDAY),
            'm' => $this->endDate->lastOfMonth(),
            default => $this->endDate->endOfDay(),
        })->endOfDay();

        $this->startDate = (match ($this->timeframe) {
            'd' => $this->endDate->copy()->subDay(),
            '7d' => $this->endDate->copy()->subDays(7),
            '30d' => $this->endDate->copy()->subDays(30),
            '60d' => $this->endDate->copy()->subDays(60),
            '90d' => $this->endDate->copy()->subDays(90),
            'w' => $this->endDate->copy()->subWeek()->dayOfWeek(CarbonInterface::MONDAY),
            'm' => $this->endDate->copy()->firstOfMonth(),
            'y' => $this->endDate->copy()->subYear(),
        })->startOfDay();

        $dateRange = match ($this->timeframe) {
            'd' => Carbon::parse($this->startDate)->hoursUntil($this->endDate),
            'w', 'm' => Carbon::parse($this->startDate)->daysUntil($this->endDate),
            'y' => Carbon::parse($this->startDate)->monthsUntil($this->endDate)->excludeStartDate(),
            default => Carbon::parse($this->startDate)->daysUntil($this->endDate)->excludeStartDate(),
        };

        $this->dateRange = collect($dateRange);
    }

    /**
     * @throws LivewireValidationException
     */
    private function validateTimeframe(): void
    {
        if (! in_array($this->timeframe, $this->availableTimeframes, true)) {
            throw new LivewireValidationException('Invalid timeframe supplied');
        }
    }
}
