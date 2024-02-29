<?php

declare(strict_types=1);

namespace App\View\Components\Alerts;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

class Flash extends Component
{
    public function __construct(public string $type = 'alert')
    {

    }

    public function render() : View
    {
        return view('components.alerts.flash');
    }

    public function message() : string
    {
        return (string) Arr::first($this->messages());
    }

    public function messages() : array
    {
        return (array) session()->get($this->type);
    }

    public function exists() : bool
    {
        return session()->has($this->type) && ! empty($this->messages());
    }

    public function class() : string
    {
        return match ($this->type) {
            'success' => 'success',
            'error' => 'danger',
            'warning' => 'warning',
            default => 'info',
        };
    }
}
