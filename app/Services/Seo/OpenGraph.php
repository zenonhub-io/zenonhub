<?php

namespace App\Services\Seo;

trait OpenGraph
{
    /**
     * Fill the Open Graph meta tags from the configuration.
     */
    protected function fillOpenGraphDefaults() : void
    {
        $this
            ->openGraphType(config('meta-tags.open_graph.type') ?: '')
            ->openGraphSiteName(config('meta-tags.open_graph.site_name') ?: '')
            ->openGraphTitle(config('meta-tags.open_graph.title') ?: '')
            ->openGraphUrl(config('meta-tags.open_graph.url') ?: '')
            ->openGraphImage(config('meta-tags.open_graph.image') ?: '');
    }

    /**
     * Update the Open Graph Title based on the regular title.
     */
    protected function autoFillOpenGraph(bool $overwrite = false) : void
    {
        if (! config('meta-tags.open_graph.auto_fill')) {
            return;
        }

        if ($this->title) {
            if ($overwrite || ! $this->getMetaByProperty('og:title')->first()?->content) {
                $this->openGraphTitle($this->title);
            }
        }

        $this->openGraphUrl(url()->current());
    }

    /**
     * Setter for the 'og:type' Meta Property.
     */
    public function openGraphType(string $value) : self
    {
        return $this->metaByProperty('og:type', $value);
    }

    /**
     * Setter for the 'og:site_name' Meta Property.
     */
    public function openGraphSiteName(string $value) : self
    {
        return $this->metaByProperty('og:site_name', $value);
    }

    /**
     * Setter for the 'og:title' Meta Property.
     */
    public function openGraphTitle(string $value) : self
    {
        return $this->metaByProperty('og:title', $value);
    }

    /**
     * Setter for the 'og:url' Meta Property.
     */
    public function openGraphUrl(string $value) : self
    {
        return $this->metaByProperty('og:url', $value);
    }

    /**
     * Setter for the 'og:image' Meta Property.
     */
    public function openGraphImage(string $value, bool $replace = true) : self
    {
        return $this->metaByProperty('og:image', $value, $replace);
    }
}
