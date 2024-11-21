<?php

declare(strict_types=1);

namespace App\Services\Discord;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;

class Message implements Arrayable
{
    public ?string $content = null;

    public ?string $username = null;

    public ?string $avatarUrl = null;

    public bool $tts = false;

    public ?array $file = null;

    public ?array $embeds = null;

    protected function __construct(?string $content = null)
    {
        if ($content !== null) {
            $this->content($content);
        }
    }

    public static function make(?string $content = null): Message
    {
        return new self($content);
    }

    public function content(string $content): Message
    {
        $this->content = Str::limit($content, 2000 - 3 /* Accounting for ellipsis */);

        return $this;
    }

    public function from(string $username, ?string $avatarUrl = null): Message
    {
        $this->username = $username;
        if ($avatarUrl !== null) {
            $this->avatarUrl = $avatarUrl;
        }

        return $this;
    }

    public function tts(bool $enabled = true): Message
    {
        $this->tts = $enabled;

        return $this;
    }

    public function file(string $contents, string $filename): Message
    {
        $this->file = [
            'name' => 'file',
            'contents' => $contents,
            'filename' => $filename,
        ];

        return $this;
    }

    public function embed(Embed $embed): Message
    {
        $this->embeds[] = $embed;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter(
            ['content' => $this->content,
                'username' => $this->username,
                'avatar_url' => $this->avatarUrl,
                'tts' => $this->tts ? 'true' : 'false',
                'file' => $this->file,
                'embeds' => $this->serializeEmbeds(), ],
            static fn ($value) => $value !== null && $value !== []);
    }

    protected function serializeEmbeds(): array
    {
        return array_map(static fn (Arrayable $embed) => $embed->toArray(), $this->embeds ?? []);
    }
}
