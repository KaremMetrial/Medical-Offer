<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\{Visit,Review,User,Offer,Provider};
use App\Http\Resources\{AppBarResource,PaginationResource,Provider\VisitResource as ProviderVisitResource,Provider\VisitDetailResource,UserCardResource,OfferResource, Provider\HomeResource};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreateVisit;
use App\Repositories\Contracts\{VisitRepositoryInterface, ReviewRepositoryInterface, UserRepositoryInterface, OfferRepositoryInterface, ProviderRepositoryInterface};

class HomeController extends BaseController
{
    public function __construct(
        protected VisitRepositoryInterface $visitRepository,
        protected ReviewRepositoryInterface $reviewRepository,
        protected UserRepositoryInterface $userRepository,
        protected OfferRepositoryInterface $offerRepository,
        protected ProviderRepositoryInterface $providerRepository
    ) {}

    /**
     * Handle the provider home page data request.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $user->load(['country']);
        $user->loadCount(['notifications as unread_notifications_count' => fn($q) => $q->whereNull('read_at')]);
        
        // Ensure the user is a provider or has a provider account
        $provider = $user->mainProvider();
        
        if (!$provider) {
            return $this->errorResponse(__('message.provider_not_found'), 404);
        }

        // Use getStats to get combined statistics in one query
        $provider = $this->providerRepository->getStats($provider->id);
        
        $providerId = $provider->id;

        // Statistics from eager-loaded aggregates
        $visitsCount = $provider->visits_count;
        $totalCollection = (float) ($provider->visits_sum_paid_amount ?: 0);
        $avgRating = (float) ($provider->reviews_avg_rating ?: 0);
        $experienceYears = $provider->experince_years;

        // Recently requested services (last 10 visits)
        $topServices = $this->getTopServices($providerId);

        $filter = $request->get('filter', 'day');
        
        // Visit Movement (Filter by day or month)
        $visitMovement = $this->visitRepository->getVisitMovement($providerId, $filter);

        // Recent Reviews
        $recentReviews = $this->reviewRepository->getRecentForProvider($providerId, 5, ['user']);

        $data = [
            'appbar' => new AppBarResource($user),
            'stats' => [
                'visits_count' => $visitsCount,
                'total_collection' => $totalCollection,
                'rating' => round($avgRating, 1),
                'experience_years' => $experienceYears,
            ],
            'top_services' => $topServices,
            'visit_movement' => $visitMovement,
            'recent_reviews' => $recentReviews,
        ];

        return $this->successResponse(new HomeResource($data), __('message.retrieved_successfully'));
    }

    /**
     * Scan QR code and return user card info.
     */
    public function getUserByCard($card_code)
    {
        $user = $this->userRepository->getDetailsByCardCode($card_code);

        if (!$user) {
            return $this->errorResponse(__('message.user_card_not_found'), 404);
        }

        if (!$user->isActive()) {
            return $this->errorResponse(__('message.user_inactive'), 403);
        }

        return $this->successResponse([
            'card_info' => new UserCardResource($user)
        ], __('message.retrieved_successfully'));
    }

    /**
     * Get paginated visits for the provider.
     */
    public function visits(Request $request)
    {
        $user = auth()->user();
        $provider = $user->mainProvider();
        
        if (!$provider) {
            return $this->errorResponse(__('message.provider_not_found'), 404);
        }

        $visits = $this->visitRepository->getPaginatedForProvider(
            $provider->id, 
            $request->get('per_page', 10), 
            $request->get('search')
        );

        return $this->successResponse([
            'visits' => ProviderVisitResource::collection($visits),
            'pagination' => new PaginationResource($visits),
            'labels' => [
                'title' => __('message.visits'),
                'search_placeholder' => __('message.search_patient_or_visit'),
                'visit_details' => __('message.visit_details'),
            ]
        ], __('message.retrieved_successfully'));
    }

    /**
     * Get top services based on visit data or static defaults.
     */
    private function getTopServices($providerId)
    {
        $offers = $this->offerRepository->getTopOffers($providerId, 3);

        return $offers->map(function ($offer) {
            return [
                'title' => $offer->name,
                'description' => $offer->description,
            ];
        })->toArray();
    }

    /**
     * Get details of a specific visit.
     */
    public function showVisit($id)
    {
        $user = auth()->user();
        $provider = $user->mainProvider();
        
        if (!$provider) {
            return $this->errorResponse(__('message.provider_not_found'), 404);
        }

        $visit = $this->visitRepository->getDetails($id, $provider->id);

        return $this->successResponse(new VisitDetailResource($visit), __('message.retrieved_successfully'));
    }

    /**
     * Register a new visit for a patient.
     */
    public function registerVisit(CreateVisit $request)
    {
        $data = $request->validated();

        $loggedUser = auth()->user();
        $provider = $loggedUser->mainProvider();

        if (!$provider) {
            return $this->errorResponse(__('message.provider_not_found'), 404);
        }

        $patient = $this->userRepository->findOrFail($data['user_id']);
        $companion = $data['companion_id'] ? $this->userRepository->findOrFail($data['companion_id']) : null;
        
        $targetUser = $companion ?? $patient;
        $activeSub = $targetUser?->subscriptions()->where('status', 'active')->latest()->first();
        
        if (!$activeSub) {
            return $this->errorResponse(__('message.no_active_subscription'), 403);
        }

        $planId = $activeSub->plan_id;
        $offers = $this->offerRepository->findInIds($data['offer_ids']);
        
        $services = [];
        foreach ($offers as $offer) {
            $services[] = [
                'offer_id' => $offer->id,
                'title' => $offer->name,
                'description' => $offer->description,
                'discount' => (string)($offer->getDiscountForPlan($planId) . '%'),
            ];
        }

        $visitData = [
            'user_id' => $patient->id,
            'companion_id' => $companion?->id,
            'provider_id' => $provider->id,
            'visit_date' => now(),
            'services' => $services,
            'paid_amount' => 0,
            'discount_amount' => 0,
            'comment' => $data['comment'],
            'status' => 'completed',
        ];

        $visit = $this->visitRepository->store($visitData);

        return $this->successResponse(
            new VisitDetailResource($this->visitRepository->getDetails($visit->id, $provider->id)),
            __('message.visit_registered_successfully')
        );
    }

    public function offers(Request $request)
    {
        $user = auth()->user();
        $provider = $user->mainProvider();
        
        if (!$provider) {
            return $this->errorResponse(__('message.provider_not_found'), 404);
        }

        $offers = $this->offerRepository->getPaginatedForProvider(
            $provider->id, 
            $request->get('per_page', 15), 
            $request->get('search')
        );

        return $this->successResponse([
            'offers' => OfferResource::collection($offers),
            'pagination' => new PaginationResource($offers),
        ], __('message.retrieved_successfully'));
    }
}
