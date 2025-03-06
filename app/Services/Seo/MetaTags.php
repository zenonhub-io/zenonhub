<?php

declare(strict_types=1);

namespace App\Services\Seo;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class MetaTags
{
    use OpenGraph;
    use Twitter;

    private bool $autoFillOverwrite = false;

    private string $title = '';

    private ?string $canonical = null;

    private Collection $meta;

    public function __construct()
    {
        $this->meta = new Collection;

        $this->fillOpenGraphDefaults();
        $this->fillTwitterDefaults();

        $this
            ->title(config('meta-tags.defaults.title') ?: '')
            ->description(config('meta-tags.defaults.description') ?: '')
            ->keywords(config('meta-tags.defaults.keywords') ?: '');

        if (config('meta-tags.auto_canonical_link')) {
            $this->canonical(request()->url());
        }

        $this->autoFillOverwrite = true;
    }

    /**
     * Setter for the title.
     *
     * @return $this
     */
    public function title(string $title, bool $withSuffix = true): self
    {
        $title = trim($title);
        $suffix = trim(config('meta-tags.title_suffix'));
        $separator = trim(config('meta-tags.title_separator'));

        $this->title = implode(' ', array_filter([
            $title,
            $withSuffix ? $separator : false,
            $withSuffix ? $suffix : false,
        ]));

        return $this->autoFill();
    }

    /**
     * Setter for the canonical URL.
     */
    public function canonical(string $url): self
    {
        $this->canonical = $url;

        return $this;
    }

    /**
     * Setter for the description.
     */
    public function description(string $description): self
    {
        $this->metaByName('description', trim($description));

        return $this->autoFill();
    }

    /**
     * Setter for the keywords.
     */
    public function keywords(mixed $keywords): self
    {
        if (is_string($keywords)) {
            $keywords = trim($keywords);
        } else {
            $keywords = collect($keywords)
                ->map(fn ($keyword) => trim($keyword))
                ->unique()
                ->implode(', ');
        }

        $this->metaByName('keywords', $keywords);

        return $this->autoFill();
    }

    /**
     * Sets a meta tag by its name attribute.
     */
    public function metaByName(string $name, string $content, bool $replace = true): self
    {
        if ($replace) {
            $this->removeMeta(['name' => $name]);
        }

        $content = trim($content);

        if (! $content) {
            return $this;
        }

        return $this->meta(['name' => $name, 'content' => $content]);
    }

    /**
     * Sets a meta tag by its property attribute.
     */
    public function metaByProperty(string $property, string $content, bool $replace = true): self
    {
        if ($replace) {
            $this->removeMeta(['property' => $property]);
        }

        $content = trim($content);

        if (! $content) {
            return $this;
        }

        return $this->meta(['property' => $property, 'content' => $content]);
    }

    /**
     * Adds a meta tag by the given attributes.
     */
    public function meta(array $attributes): self
    {
        $this->meta->push(new MetaTag($attributes));

        return $this;
    }

    /**
     * Remove a meta tag that matches the given attributes
     */
    public function removeMeta(array $attributes): self
    {
        $this->meta = $this->meta->reject(fn (MetaTag $meta) => $meta->hasAllAttributes($attributes));

        return $this;
    }

    /**
     * Get a Meta instance by name.
     */
    public function getMetaByName(string $name): Collection
    {
        return $this->meta->filter(fn (MetaTag $meta) => $meta->name === $name);
    }

    /**
     * Get a Meta instance by property.
     */
    public function getMetaByProperty(string $property): Collection
    {
        return $this->meta->filter(fn (MetaTag $meta) => $meta->property === $property);
    }

    /**
     * Getter for the title.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Returns a HtmlString with the title and the meta tags.
     */
    public function renderTags(): Htmlable
    {
        return new HtmlString($this->renderTitle() . PHP_EOL . $this->renderMeta());
    }

    /**
     * Use the traits the fill the meta properties with the default data.
     */
    private function autoFill(): self
    {
        $this->autoFillOpenGraph($this->autoFillOverwrite);
        $this->autoFillTwitter($this->autoFillOverwrite);

        return $this;
    }

    /**
     * Renders a Title tag with the title.
     */
    private function renderTitle(): string
    {
        $title = e($this->title);

        return "<title>{$title}</title>";
    }

    /**
     * Renders all meta tags.
     */
    private function renderMeta(): string
    {
        return $this->meta
            ->map(fn (MetaTag $meta) => $meta->render())
            ->when(e($this->canonical), function (Collection $collection, string $href) {
                $collection->prepend(
                    "<link rel=\"canonical\" href=\"{$href}\">"
                );
            })
            ->implode(PHP_EOL);
    }
}
