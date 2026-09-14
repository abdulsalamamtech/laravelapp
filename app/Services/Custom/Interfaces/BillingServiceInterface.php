<?php

declare(strict_types=1);

namespace App\Services\Custom\Interfaces;

interface BillingServiceInterface
{
    public function initializePayment(array $payload);

    public function verifyTransaction(string $reference);

    public function createCustomer(array $payload);

    public function fetchCustomer(string $customerCode);

    public function createPlan(array $payload);

    public function fetchPlan(string $planCode);

    public function createSubscription(array $payload);

    public function fetchSubscription(string $subscriptionCode);

    public function cancelSubscription(string $subscriptionCode);

    public function listPlans();

    public function getPlans();

    public function getPlanById(string $planId);

    public function getPlan(string $planId);

    public function updatePlan(string $planId, array $data);
}
