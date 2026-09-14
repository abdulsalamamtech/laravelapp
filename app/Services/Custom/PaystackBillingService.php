<?php

namespace App\Services\Custom;

use App\Libraries\Paystack;
use App\Services\Custom\Interfaces\BillingServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PaystackBillingService implements BillingServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(private readonly Paystack $paystack) {}

    // ----------------------- PAYMENT METHODS ----------------------- //
    /**
     * Create a payment using the Paystack library
     *
     * @return payment response [No interface]
     */
    // public function createPayment()
    // {
    //     $data = [
    //         'name' => 'John Doe',
    //         'amount' => 100 * 10, // Paystack expects amount in kobo
    //         'email' => 'hello@example.com',
    //         'payment_id' => uniqid(), // Unique payment ID
    //         'callback_url' => 'http://localhost/payment/callback', // Define this route to handle payment callbacks
    //     ];

    //     // Use Paystack library to create a payment
    //     return Paystack::makePayment($data);
    // }

    /**
     * Initialize a Paystack payment.
     */
    public function initializePayment($data)
    {
        return Paystack::initiatePayment($data);
    }

    /**
     * Verify a Paystack transaction.
     */
    public function verifyTransaction($reference)
    {
        return Paystack::verify($reference);
    }

    /**
     * Fetch a Paystack customer.
     */
    public function fetchCustomer($customerId)
    {
        return $this->getCustomer($customerId);
    }

    /**
     * Fetch a Paystack plan.
     */
    public function fetchPlan($planId)
    {
        return $this->getPlan($planId);
    }

    /**
     * Fetch a Paystack subscription.
     */
    public function fetchSubscription($subscriptionId)
    {
        return $this->getSubscription($subscriptionId);
    }

    /**
     * Cancel a Paystack subscription.
     */
    public function cancelSubscription($subscriptionId)
    {
        if (method_exists(Paystack::class, 'cancelSubscription')) {
            return Paystack::cancelSubscription($subscriptionId);
        }

        return Paystack::disableSubscription($subscriptionId, [
            'token' => 'AUTH_xxxxx',
        ]);
    }

    // ----------------------- PLAN METHODS ----------------------- //
    /**
     * Create a new subscription plan
     */
    public function createPlan($data)
    {
        $plan = Paystack::createPlan($data);
        Log::info('Creating a new plan with data: ', [$data, $plan]);

        if ($plan['success']) {
            // NOTE: Persisting the plan to the DB is deactivated until App\Models\Plan is restored.
            Log::info('Paystack plan created (DB persistence deactivated): ', [$plan['data']]);

            return $plan['data'];
        }

        // return response()->json($plan, 400);
        return null;
    }

    /**
     * Get all plans [API]
     */
    public function listPlans()
    {
        $plans = Paystack::listPlans([
            'per_page' => 10,
            'page' => 1,
            'status' => 'active', // optional
        ]);

        return $plans['data']; // Return the list of plans
    }

    /**
     * Get all plans [DB]
     *
     * NOTE: Deactivated until App\Models\Plan is restored.
     */
    public function getPlans(): array
    {
        Log::info('Paystack getPlans() deactivated - App\Models\Plan is not available');

        return [];
    }

    /**
     * Get all active plans [DB]
     *
     * NOTE: Deactivated until App\Models\Plan is restored.
     */
    public function getActivePlans(): array
    {
        Log::info('Paystack getActivePlans() deactivated - App\Models\Plan is not available');

        return [];
    }

    /**
     * Get a specific plan [DB]
     *
     * NOTE: Deactivated until App\Models\Plan is restored.
     */
    public function getPlanById($planId): null
    {
        Log::info('Paystack getPlanById() deactivated - App\Models\Plan is not available', ['plan_id' => $planId]);

        return null;
    }

    // Get a specific plan
    public function getPlan($planId)
    {
        return $plan = Paystack::getPlan($planId);
    }

    // Update a plan
    public function updatePlan($planId, $data = [])
    {
        return Paystack::updatePlan($planId, $data);
    }

    // ----------------------- CUSTOMERS METHODS ----------------------- //
    // Create a new customer
    public function createCustomer($data)
    {
        if (! isset($data['email']) || ! isset($data['first_name']) || ! isset($data['last_name'])) {
            Log::error('Missing required customer data', ['data' => $data]);

            return null;
        }

        $customer = Paystack::createCustomer($data);

        if ($customer['success']) {
            $customerId = $customer['data']['customer_code'];

            // Save customerId to your database
            return $customer['data'];
        }

        return null;
    }

    // Get all customers
    public function listCustomers()
    {
        $customers = Paystack::listCustomers([
            'per_page' => 10,
            'page' => 1,
        ]);

        return $customers['data'];
    }

    // Get a specific customer
    public function getCustomer($customerId)
    {
        $customer = Paystack::getCustomer($customerId);
        if (! $customer['success']) {
            Log::error('Failed to retrieve customer', ['customer_id' => $customerId]);

            return null;
        }

        return $customer['data'];
    }

    // Update customer details
    public function updateCustomer($customerId, $data = [])
    {
        $customer = Paystack::updateCustomer($customerId, $data);

        return $customer['data'] ?? null;
    }

    // Validate customer account details
    public function validateCustomer($data = [])
    {
        $validation = Paystack::validateCustomer($data);

        return $validation['data'] ?? null;
    }

    // ----------------------- SUBSCRIPTION METHODS ----------------------- //

    // Create a new subscription for a customer
    public function createSubscription($payload)
    {
        $subscription = Paystack::createSubscription($payload);

        if ($subscription['success']) {
            $subscriptionCode = $subscription['data']['subscription_code'] ?? null;

            // Save subscription details to your database
            return $subscription['data'];
        }

        return null;
    }

    // Get all subscriptions (with optional filtering)
    public function listSubscriptions()
    {
        $subscriptions = Paystack::listSubscriptions([
            'status' => 'active', // active, cancelled, or completed
            'per_page' => 10,
            'page' => 1,
        ]);

        return $subscriptions['data'] ?? [];
    }

    // Get a specific subscription
    public function getSubscription($subscriptionId)
    {
        $subscription = Paystack::getSubscription($subscriptionId);

        return $subscription['data'];
    }

    // Enable a disabled subscription
    public function enableSubscription($subscriptionId)
    {
        $subscription = Paystack::enableSubscription($subscriptionId, [
            'token' => 'AUTH_xxxxx', // authorization token
        ]);

        return $subscription['data'];
    }

    // Disable an active subscription
    public function disableSubscription($subscriptionId)
    {
        $subscription = Paystack::disableSubscription($subscriptionId, [
            'token' => 'AUTH_xxxxx', // authorization token
        ]);

        return $subscription['data'];
    }

    // SUBSCRIBE USER TO A PLAN Paystack
    public function subscribeToPlan(array $data, $company): null
    {
        // NOTE: Deactivated until the Company/Plan domain (App\Models\Plan) is restored.
        Log::info('Paystack subscribeToPlan() deactivated - App\Models\Plan is not available', [
            'plan_id' => $data['plan_id'] ?? null,
        ]);

        return null;
    }

    // getTransactionHistory
    public function getTransactionHistory($company): Collection
    {
        Log::info('Paystack getTransactionHistory() deactivated - Company domain is not available');

        return collect();
    }

    // Call back
    public function handleCallback(Request $request)
    {
        Log::info('Paystack service - Paystack callback received', $request->all());
        $data = $request->all();
        Log::info('Paystack callback data', $data);

        $reference = $request->input('reference');
        // $code = $request->input('code');

        return $this->paystack->verify($reference);
    }
}
