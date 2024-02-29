<?php

declare(strict_types=1);

namespace App\View\Components\Navbar;

use Illuminate\Support\Str;
use Illuminate\View\Component;

abstract class BaseItem extends Component
{
    public function __construct(
        public string $title,
        public ?string $route = null,
        public ?bool $isActive = null,
        public ?string $icon = null,
        public ?string $svg = null
    ) {
        if (is_null($route)) {
            $route = Str::lower($title);
            $route = Str::slug($route);
        }

        if (is_null($isActive)) {
            $isActive = request()->routeIs($route);
        }

        $this->route = $route;
        $this->isActive = filter_var($isActive, FILTER_VALIDATE_BOOLEAN);
    }
}
