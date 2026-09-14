<?php

namespace App\Services\Custom;

use App\Libraries\Flutterwave;
use App\Models\Transaction;
use App\Services\Custom\Interfaces\BillingServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class FlutterwaveBillingService implements BillingServiceInterface
{
    protected string $baseUrl;

    protected string $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.flutterwave.url') ?? 'https://api.flutterwave.com/v3';
        $this->secretKey = config('services.flutterwave.secret');
    }

    /**
     * Create a new class instance.
     */
    // public function __construct(Paystack $paystack)
    // {
    //     $this->paystack = $paystack;
    // }

    // ----------------------- PAYMENT METHODS ----------------------- //
    /**
     * Create a payment using the Flutterwave library
     *
     * @return array
     */
    public function createPayment(array $data = [])
    {
        return Flutterwave::makePayment($data);
    }

    public function initializePayment(array $payload)
    {
        return $this->createPayment($payload);
    }

    public function verifyTransaction(string $reference)
    {
        return Flutterwave::verifyPaymentByReference($reference);
    }

    public function fetchCustomer(string $customerCode)
    {
        return $this->getCustomer($customerCode);
    }

    public function fetchPlan(string $planCode)
    {
        return $this->getPlan($planCode);
    }

    public function fetchSubscription(string $subscriptionCode)
    {
        return $this->getSubscription($subscriptionCode);
    }

    public function cancelSubscription(string $subscriptionCode)
    {
        return $this->disableSubscription($subscriptionCode);
    }

    // ----------------------- PLAN METHODS ----------------------- //
    /**
     * Create a new subscription plan
     */
    public function createPlan($data)
    {
        Log::info('Creating a new flutterwave plan with data: '.json_encode($data));

        $plan = Flutterwave::createPlan($data);
        Log::info('Creating a new flutterwave plan response: '.json_encode($plan));

        // [
        //   "success" => true
        //   "message" => "Payment plan created"
        //   "data" => array:9 [
        //     "id" => 237885
        //     "name" => "Business Plan"
        //     "amount" => 2500
        //     "interval" => "monthly"
        //     "duration" => 0
        //     "status" => "active"
        //     "currency" => "NGN"
        //     "plan_token" => "rpp_59e899626e49a4b4e736"
        //     "created_at" => "2026-06-18T05:22:27.000Z"
        //   ]
        // ]

        if ($plan['success'] && $plan['data']) {
            // NOTE: Persisting the plan to the DB is deactivated until App\Models\Plan is restored.
            Log::info('Flutterwave plan created (DB persistence deactivated): ', [$plan['data']]);

            return $plan['data'];
        }

        return false;
    }

    /**
     * Get all plans [API]
     */
    public function listPlans()
    {
        $plans = Flutterwave::listPlans([
            'size' => 10,
            'page' => 1,
        ]);

        return $plans['data'] ?? [];
    }

    /**
     * Get all plans [DB]
     *
     * NOTE: Deactivated until App\Models\Plan is restored.
     */
    public function getPlans(): array
    {
        Log::info('Flutterwave getPlans() deactivated - App\Models\Plan is not available');

        return [];
    }

    /**
     * Get a specific plan [DB]
     *
     * NOTE: Deactivated until App\Models\Plan is restored.
     */
    public function getPlanById($planId): null
    {
        Log::info('Flutterwave getPlanById() deactivated - App\Models\Plan is not available', ['plan_id' => $planId]);

        return null;
    }

    // Get a specific plan
    public function getPlan($planId)
    {
        return Flutterwave::getPlan($planId);
    }

    // Update a plan
    public function updatePlan($planId, $data)
    {
        return Flutterwave::updatePlan($planId, $data);
    }

    // ----------------------- CUSTOMERS METHODS (v4) ----------------------- //
    // Create a new customer
    public function createCustomer($data)
    {
        if (! isset($data['email']) || ! isset($data['first_name']) || ! isset($data['last_name'])) {
            Log::error('Missing required customer data', ['data' => $data]);

            return null;
        }

        $last10 = $data['phone_number'] ? substr($data['phone_number'], -10) : '2349000000000';
        $data['phone'] = [
            'country_code' => '234',
            'number' => $last10,
        ];
        $customer = Flutterwave::createCustomer($data);

        if ($customer['success'] && $customer['data']) {
            // $customerId = $customer['data']['id'];
            // Save customerId to your database
            return $customer['data'];
        }

        return null;
    }

    // Get all customers
    public function listCustomers()
    {
        $customers = Flutterwave::listCustomers([
            'size' => 10,
            'page' => 1,
        ]);

        return $customers['data'] ?? [];
    }

    // Get a specific customer
    public function getCustomer($customerId)
    {
        $customer = Flutterwave::getCustomer($customerId);
        if (! $customer['success']) {
            Log::error('Failed to retrieve customer', ['customer_id' => $customerId]);

            return null;
        }

        return $customer['data'];
    }

    // Update customer details
    public function updateCustomer($customerId, $data)
    {
        $customer = Flutterwave::updateCustomer($customerId, $data);

        return $customer['data'] ?? null;
    }

    // Resolve customer bank details or update customer data
    public function validateCustomer(array $data)
    {
        return Flutterwave::getCustomer($data['customer_id'] ?? '');
    }

    // ----------------------- SUBSCRIPTION METHODS ----------------------- //

    // Create a new subscription for a customer
    public function createSubscription($payload)
    {
        $subscription = Flutterwave::createSubscription($payload);

        if ($subscription['success']) {
            return $subscription['data'];
        }

        return null;
    }

    // Get all subscriptions (with optional filtering)
    public function listSubscriptions()
    {
        $subscriptions = Flutterwave::listSubscriptions([
            'page' => 1,
            'size' => 10,
            'status' => 'active',
        ]);

        return $subscriptions['data'] ?? [];
    }

    // Get a specific subscription
    public function getSubscription($subscriptionId)
    {
        $subscription = Flutterwave::getSubscription($subscriptionId);

        return $subscription['data'] ?? null;
    }

    // Enable a disabled subscription
    public function enableSubscription($subscriptionId, $data = [])
    {
        $subscription = Flutterwave::enableSubscription($subscriptionId, $data);

        return $subscription['data'] ?? null;
    }

    // Disable an active subscription
    public function disableSubscription($subscriptionId, $data = [])
    {
        $subscription = Flutterwave::disableSubscription($subscriptionId, $data);

        return $subscription['data'] ?? null;
    }

    /**
     * Subscribe a company to a plan on Flutterwave.
     *
     * NOTE: Deactivated until the Company/Plan domain (App\Models\Plan) is restored.
     */
    public function subscribeToPlan($company, array $data): null
    {
        Log::info('Flutterwave subscribeToPlan() deactivated - App\Models\Plan is not available', [
            'plan_id' => $data['plan_id'] ?? null,
        ]);

        return null;
    }

    public function subscribeToPlanV4($company, array $data): null
    {
        Log::info('Flutterwave subscribeToPlanV4() deactivated - App\Models\Plan is not available', [
            'plan_id' => $data['plan_id'] ?? null,
        ]);

        return null;
    }

    // getTransactionHistory
    public function getTransactionHistory($company): Collection
    {
        Log::info('Flutterwave getTransactionHistory() deactivated - Company domain is not available');

        return collect();
    }

    public function handleCallback(Request $request)
    {
        // https://veriscore-staging.vercel.app/api/flutterwave/callback
        // ?status=session_expired&tx_ref=flw_6a33a02a6ec583.02378496&transaction_id=null

        // https://veriscore-staging.vercel.app/api/flutterwave/callback
        // ?status=successful&tx_ref=order_1234&transaction_id=10307546

        Log::info('Flutterwave service - Flutterwave callback received', $request->all());
        $data = $request->all();
        Log::info('Flutterwave callback data', $data);
        $url = config('services.flutterwave.error');

        try {

            $reference = $request->input('tx_ref');
            $status = $request->input('status');
            $transaction_id = $request->input('transaction_id');

            $transactionResponse = $this->verifyTransaction($reference);
            Log::info('Flutterwave service verify transaction response: ', [$transactionResponse]);
            $transactionFromDB = Transaction::where('reference', $reference)->first();
            Log::info('Flutterwave service - Transaction from DB: ', [$transactionFromDB]);
            if ($transactionResponse['success'] && $transactionFromDB) {
                if ($this->isPaymentValid($transactionResponse, $transactionFromDB)) {

                    // NOTE: Plan activation (App\Services\Company\CompanyService) is
                    // deactivated until the Company/Plan domain is restored.
                    $transactionFromDB->status = 'successful';
                    $transactionFromDB->save();

                    // success
                    $url = config('services.flutterwave.success');
                }
            }
        } catch (\Throwable $throwable) {
            // throw $th;
            Log::alert('Flutterwave service - there was an error: ', [$throwable]);

            return $url;
        }

        Log::alert('Flutterwave service - Redirect away to frontend: ', [$url]);

        // redirect away
        return $url;
    }

    /**
     * @return mixed[]
     */
    public function validatePayment(string $reference): array
    {
        // https://veriscore-staging.vercel.app/api/flutterwave/callback
        // ?status=session_expired&tx_ref=flw_6a33a02a6ec583.02378496&transaction_id=null

        // https://veriscore-staging.vercel.app/api/flutterwave/callback
        // ?status=successful&tx_ref=order_1234&transaction_id=10307546

        Log::info('Flutterwave service - Flutterwave validate payment received data', [$reference]);
        $url = config('services.flutterwave.error');
        $data = [
            'success' => false,
            'message' => 'payment can not be verify, please try again later!',
            'url' => $url,
        ];

        try {

            $transactionResponse = $this->verifyTransaction($reference);
            Log::info('Flutterwave service verify transaction response: ', [$transactionResponse]);
            $transactionFromDB = Transaction::where('reference', $reference)->first();
            Log::info('Flutterwave service - Transaction from DB: ', [$transactionFromDB]);
            if ($transactionResponse['success'] && $transactionFromDB) {
                if ($this->isPaymentValid($transactionResponse, $transactionFromDB)) {

                    // NOTE: Plan activation (App\Services\Company\CompanyService) is
                    // deactivated until the Company/Plan domain is restored.
                    $transactionFromDB->status = 'successful';
                    $transactionFromDB->save();

                    // success
                    return [
                        'success' => true,
                        'message' => 'Payment verified successfully.',
                        'url' => config('services.flutterwave.success'),
                    ];
                }
            }
        } catch (\Throwable $throwable) {
            // throw $th;
            Log::alert('Flutterwave service - there was an error: ', [$throwable]);

            // redirect away using url
            return $data;
        }

        return $data;
    }

    /**
     * Check if payment is valid
     */
    public function isPaymentValid(array $transactionResponse, array $transactionFromDB): bool
    {
        Log::alert('Flutterwave payment verification - payment from flutterwave and DB', [
            'flutterwave_transaction_record' => $transactionResponse,
            'db_transaction_record' => $transactionFromDB,
        ]);

        $data = $transactionResponse['data'];
        $expectedTxRef = $transactionFromDB['reference'];
        $expectedAmount = $transactionFromDB['amount'];
        $expectedCurrency = $transactionFromDB['currency'];

        // 1. Check the top-level success [success]
        if (! $transactionResponse['success']) {
            return false;
        }

        Log::alert('Flutterwave payment verification - response shows success');

        // 2. Check the transaction status
        if ($data['status'] !== 'successful') {
            return false;
        }

        Log::alert('Flutterwave payment verification - payment shows success');

        // 3. Check the amount matches what you expected Naira to Kobo
        if ((int) ($data['charged_amount'] * 100) < $expectedAmount) {
            return false;
        }

        Log::alert('Flutterwave payment verification - payment amount match');

        // 4. Check the currency matches
        if ($data['currency'] !== $expectedCurrency) {
            return false;
        }

        Log::alert('Flutterwave payment verification - payment currency match');

        // 5. Check the reference matches
        if ($data['tx_ref'] !== $expectedTxRef) {
            return false;
        }

        Log::alert('Flutterwave payment verification - payment reference match');

        return true;
    }
}
