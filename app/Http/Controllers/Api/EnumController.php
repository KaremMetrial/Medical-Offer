<?php

namespace App\Http\Controllers\Api;

use App\Enums\{
    GenderType,
    RatingType,
    DiscountType,
    SectionType,
    RelationshipType,
    WalletTransactionType,
    NotificationType
};

class EnumController extends BaseController
{
    public function genders()
    {
        return $this->successResponse([
            'label' => __('message.genders'),
            'items' => GenderType::options()
        ]);
    }
    public function ratings()
    {
        return $this->successResponse([
            'label' => __('message.ratings'),
            'items' => RatingType::options()
        ]);
    }
    public function discounts()
    {
        return $this->successResponse([
            'label' => __('message.discounts'),
            'items' => DiscountType::options()
        ]);
    }
    public function sections()
    {
        return $this->successResponse([
            'label' => __('message.sections'),
            'items' => SectionType::options()
        ]);
    }
    public function relationshipTypes()
    {
        return $this->successResponse([
            'label' => __('message.relationship_types'),
            'items' => RelationshipType::options()
        ]);
    }
    public function walletTransactionTypes()
    {
        return $this->successResponse([
            'label' => __('message.wallet_transaction_types'),
            'items' => WalletTransactionType::options()
        ]);
    }
    public function notificationTypes()
    {
        return $this->successResponse([
            'label' => __('message.notification_types'),
            'items' => NotificationType::options()
        ]);
    }
}
