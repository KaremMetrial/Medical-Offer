<?php

namespace App\Services;

use App\Models\CardRequest;
use App\Models\User;
use App\Repositories\Contracts\CardRequestRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CardRequestService
{
    public function __construct(
        protected CardRequestRepositoryInterface $repository,
        protected CountryContext $countryContext
    ){}

    /**
     * Get fees for membership card request.
     */
    public function getFees(User $user): array
    {
        $hasRequestedBefore = $this->repository->hasUserRequestedBefore($user->id);

        // Logic based on requirements (Free for the first time)
        $issuanceFee = $hasRequestedBefore ? 50.00 : 0.00;
        $deliveryFee = 0.00; // Free delivery as requested
        
        $country = $this->countryContext->getCountry() ?? $user->country;

        return [
            'issuance_fee' => $issuanceFee,
            'delivery_fee' => $deliveryFee,
            'total_amount' => $issuanceFee + $deliveryFee,
            'is_free' => !$hasRequestedBefore,
            'currency_symbol' => $country?->currency_symbol ?? 'EGP',
        ];
    }

    /**
     * Create a new card request.
     */
    public function createRequest(User $user, array $data): CardRequest
    {
        $fees = $this->getFees($user);
        
        return $this->repository->create($data + [
            'user_id'        => $user->id,
            'issuance_fee'   => $fees['issuance_fee'],
            'delivery_fee'   => $fees['delivery_fee'],
            'total_amount'   => $fees['total_amount'],
            'status'         => 'pending',
        ]);
    }

    /**
     * Get user's latest card request with current status.
     */
    public function getLatestRequest(User $user): ?CardRequest
    {
        return $this->repository->getLatestByUser($user->id);
    }
}
