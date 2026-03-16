<?php

namespace App\Repositories\Contracts;

interface SubscriptionRepositoryInterface extends BaseRepositoryInterface
{
    public function getUserSubscription($userId, array $relations = ['plan.translations']);
    public function getUserInvoices($userId);
    public function createSubscription(array $data);
    public function activateSubscription($subscriptionId, $transactionId = null);
}
