<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count()),
            Stat::make('Active Users', User::where('last_seen_at', '>', now()->subDays(7))->count()),
            Stat::make('Verified Users', User::whereNotNull('email_verified_at')->count()),
            //
        ];
    }
}
