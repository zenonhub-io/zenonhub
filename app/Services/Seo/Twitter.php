<?php

namespace App\Services\Seo;

trait Twitter
{
    /**
     * Fill the Twitter meta tags from the configuration.
     */
    protected function fillTwitterDefaults(): void
    {
        $this
            ->twitterCard(config('meta_tags.twitter.card') ?: '')
            ->twitterSite(config('meta_tags.twitter.site') ?: '')
            ->twitterTitle(config('meta_tags.twitter.title') ?: '')
            ->twitterDescription(config('meta_tags.twitter.description') ?: '')
            ->twitterImage(config('meta_tags.twitter.image') ?: '');
    }

    /**
     * Update the Twitter Title and Description based on the regular title.
     */
    protected function autoFillTwitter(bool $overwrite = false): void
    {
        if (! config('meta_tags.twitter.auto_fill')) {
            return;
        }

        if ($this->title) {
            if ($overwrite || ! $this->getMetaByName('twitter:title')->first()?->content) {
                $this->twitterTitle($this->title);
            }
        }

        if ($meta = $this->getMetaByName('description')->first()) {
            if ($overwrite || ! $this->getMetaByName('twitter:description')->first()?->content) {
                $this->twitterDescription($meta->content);
            }
        }
    }

    /**
     * Setter for the 'twitter:card' Meta Name.
     */
    public function twitterCard(string $value): self
    {
        $this->metaByName('twitter:card', trim($value));

        return $this;
    }

    /**
     * Setter for the 'twitter:site' Meta Name.
     */
    public function twitterSite(string $value): self
    {
        $this->metaByName('twitter:site', trim($value));

        return $this;
    }

    /**
     * Setter for the 'twitter:title' Meta Name.
     */
    public function twitterTitle(string $value): self
    {
        $this->metaByName('twitter:title', trim($value));

        return $this;
    }

    /**
     * Setter for the 'twitter:description' Meta Name.
     */
    public function twitterDescription(string $value): self
    {
        $this->metaByName('twitter:description', trim($value));

        return $this;
    }

    /**
     * Setter for the 'twitter:image' Meta Name.
     */
    public function twitterImage(string $value): self
    {
        $this->metaByName('twitter:image', trim($value));

        return $this;
    }
}
