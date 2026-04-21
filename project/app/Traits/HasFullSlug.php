<?php

namespace App\Traits;

trait HasFullSlug
{
    /**
     * Get the full slug URL.
     *
     * @return string
     */
    public function getFullSlugAttribute(): string
    {
        $domain = 'https://www.myvideoinvites.com/';
        return rtrim($domain, '/') . '/' . ltrim($this->slug ?? '', '/');
    }
}
