<?php

declare(strict_types=1);

namespace App\Services\Seo;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use JsonSerializable;

class MetaTag implements Arrayable, JsonSerializable
{
    /**
     * Creates a new instance.
     */
    public function __construct(
        private readonly array $attributes
    ) {}

    /**
     * Dynamic getter for the attributes.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Renders the meta tag with all attributes.
     */
    public function render(): string
    {
        $attributes = collect($this->attributes)->map(function ($value, $key) {
            $value = e($value);

            return "{$key}=\"{$value}\"";
        })->implode(' ');

        return "<meta {$attributes} />";
    }

    /**
     * Returns a boolean whether the given attributes match.
     */
    public function hasAllAttributes(array $attributes): bool
    {
        foreach ($attributes as $key => $value) {
            if (! array_key_exists($key, $this->attributes)) {
                return false;
            }

            if ($this->attributes[$key] !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns an array with all attributes.
     */
    public function toArray(): array
    {
        return Collection::make($this->attributes)
            ->mapWithKeys(fn ($value, $key) => [$key => e($value)])
            ->all();
    }

    /**
     * Returns the array from the 'toArray' method.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
