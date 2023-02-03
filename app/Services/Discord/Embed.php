<?php

namespace App\Services\Discord;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use App\Services\Discord\Contracts\DiscordWebHook;

class Embed implements Arrayable
{
    public ?string $title = null;
    public ?string $description = null;
    public ?string $url = null;
    public ?int $color = null;
    public ?array $footer = null;
    public ?array $image = null;
    public ?array $thumbnail = null;
    public ?array $author = null;
    public ?array $fields = null;
    public ?string $timestamp = null;

    public static function make(): Embed
    {
        return new self();
    }

    protected function __construct()
    {
    }

    public function title(string $title, string $url = ''): Embed
    {
        $this->title = $title;
        $this->url = $url;
        return $this;
    }

    public function description(string $description): Embed
    {
        $this->description = Str::limit($description,
            2000 - 3 /* Accounting for ellipsis */);
        return $this;
    }

    public function color(int $code): Embed
    {
        $this->color = $code;
        return $this;
    }

    public function footer(string $text, string $icon_url = ''): Embed
    {
        $this->footer = [
            'text' => $text,
            'icon_url' => $icon_url,
        ];
        return $this;
    }

    public function image(string $url): Embed
    {
        $this->image = ['url' => $url,];

        return $this;
    }

    public function thumbnail(string $url): Embed
    {
        $this->thumbnail = ['url' => $url,];
        return $this;
    }

    public function author(string $name, string $url = '', string $icon_url = ''): Embed
    {
        $this->author = [
            'name' => $name,
            'url' => $url,
            'icon_url' => $icon_url,
        ];

        return $this;
    }

    public function field(string $name, string $value, bool $inline = false): Embed
    {
        $this->fields[] = EmbedField::make($name, $value, $inline);
        return $this;
    }

    public function timestamp(string $timestamp): Embed
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function toArray(): array
    {
        return array_filter(
            [
                'title' => $this->title,
                'description' => $this->description,
                'url' => $this->url,
                'color' => $this->color,
                'footer' => $this->footer,
                'image' => $this->image,
                'thumbnail' => $this->thumbnail,
                'author' => $this->author,
                'fields' => $this->serializeFields(),
                'timestamp' => $this->timestamp,
            ],
            static function ($value) {
                return $value !== null && $value !== "" && $value !== [];
            });
    }

    protected function serializeFields(): array
    {
        return array_map(static function (Arrayable $field) {
            return $field->toArray();
        }, $this->fields ?? []);
    }
}
