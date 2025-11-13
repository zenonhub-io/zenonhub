<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class SiteLink extends Widget
{
    protected static ?int $sort = -2;

    protected static bool $isLazy = false;

    protected string $view = 'filament.widgets.site-link';
}
