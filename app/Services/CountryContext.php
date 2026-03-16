<?php

namespace App\Services;

/**
 * Singleton service to hold the global country context for the current request.
 */
class CountryContext
{
    protected ?int $countryId = null;

    /**
     * Set the current country ID.
     */
    public function setCountryId(?int $id): void
    {
        $this->countryId = $id;
    }

    /**
     * Get the current country ID.
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * Check if a country context is set.
     */
    public function hasCountryId(): bool
    {
        return !is_null($this->countryId);
    }
}
