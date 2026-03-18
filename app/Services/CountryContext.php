<?php

namespace App\Services;

/**
 * Singleton service to hold the global country context for the current request.
 */
class CountryContext
{
    protected ?int $countryId = null;
    protected ?\App\Models\Country $country = null;

    /**
     * Set the current country ID.
     */
    public function setCountryId(?int $id): void
    {
        $this->countryId = $id;
        $this->country = null; // Reset cached model
    }

    /**
     * Get the current country ID.
     */
    public function getCountryId(): ?int
    {
        return $this->countryId;
    }

    /**
     * Get the current country model.
     */
    public function getCountry(): ?\App\Models\Country
    {
        if ($this->country === null && $this->hasCountryId()) {
            $this->country = \App\Models\Country::find($this->countryId);
        }
        return $this->country;
    }

    /**
     * Check if a country context is set.
     */
    public function hasCountryId(): bool
    {
        return !is_null($this->countryId);
    }
}
