<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Svg extends Component
{
    private string $path;

    public function __construct(
        public string $file,
    ) {
        $this->file = trim($file, '/');
        $this->path = public_path("build/svg/{$file}.svg");
    }

    public function render() : View
    {
        return view('components.svg');
    }

    public function svg() : string
    {
        if (! file_exists($this->path)) {
            return 'Error';
        }

        return file_get_contents($this->path);
    }
}
