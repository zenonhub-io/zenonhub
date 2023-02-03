<?php

function svg($file, $class = null, $style = null)
{
    if (Str::startsWith('/', $file)) {
        $file = ltrim($file, '/');
    }
    $path = public_path("svg/{$file}.svg");

    if (file_exists($path)) {
        $svg = file_get_contents($path);
        return "<span class=\"svg-icon svg-icon-{$file} {$class}\" style=\"{$style}\"'>{$svg}</span>";
    }
    return "<span class=\"svg-icon mod-missing {$class}\">No icon</span>";
}
