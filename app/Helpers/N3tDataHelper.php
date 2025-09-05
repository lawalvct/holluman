<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class N3tDataHelper
{
    private $baseUrl = 'https://n3tdata.com/api';
    private $username;
    private $password;

    public function __construct()
    {
        // Get credentials from settings or environment
        $this->username = env('N3TDATA_USERNAME', 'holluman@gmail.com');
        $this->password = env('N3TDATA_PASSWORD', 'PecuGold123@');
    }

    /**
     * Get access token from N3tdata API
     */
    public function getAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->username . ':' . $this->password),
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/user');

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === 'success') {
                return [
                    'success' => true,
                    'access_token' => $result['AccessToken'],
                    'balance' => $result['balance'],
                    'username' => $result['username']
                ];
            }

            Log::error('N3tdata access token request failed', [
                'response' => $result,
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Failed to get access token'
            ];

        } catch (\Exception $e) {
            Log::error('N3tdata access token exception', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Connection error while getting access token'
            ];
        }
    }

    /**
     * Purchase data subscription for a phone number
     */
    public function purchaseDataSubscription($networkId, $phoneNumber, $dataPlanId, $requestId = null)
    {
        try {
            // Get access token first
            $tokenResult = $this->getAccessToken();
            if (!$tokenResult['success']) {
                return $tokenResult;
            }

            $accessToken = $tokenResult['access_token'];

            // Generate request ID if not provided
            if (!$requestId) {
                $requestId = 'Data_' . time() . '_' . uniqid();
            }

            // Prepare payload
            $payload = [
                'network' => (int)$networkId,
                'phone' => $phoneNumber,
                'data_plan' => (int)$dataPlanId,
                'bypass' => false,
                'request-id' => $requestId
            ];

            // Make the API call
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->baseUrl . '/data', $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] === 'success') {
                Log::info('N3tdata subscription successful', [
                    'request_id' => $requestId,
                    'phone' => $phoneNumber,
                    'network' => $result['network'] ?? $networkId,
                    'dataplan' => $result['dataplan'] ?? null
                ]);

                return [
                    'success' => true,
                    'data' => $result,
                    'message' => $result['message'] ?? 'Data subscription successful'
                ];
            }

            Log::error('N3tdata subscription failed', [
                'request_id' => $requestId,
                'response' => $result,
                'status' => $response->status(),
                'payload' => $payload
            ]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Data subscription failed',
                'data' => $result
            ];

        } catch (\Exception $e) {
            Log::error('N3tdata subscription exception', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber,
                'network_id' => $networkId,
                'data_plan_id' => $dataPlanId
            ]);

            return [
                'success' => false,
                'message' => 'Connection error while processing data subscription'
            ];
        }
    }

    /**
     * Get account balance
     */
    public function getBalance()
    {
        $tokenResult = $this->getAccessToken();
        if ($tokenResult['success']) {
            return [
                'success' => true,
                'balance' => $tokenResult['balance']
            ];
        }

        return $tokenResult;
    }

    /**
     * Map local network ID to N3tdata network ID
     * You may need to adjust these mappings based on your network setup
     */
    public function mapNetworkId($localNetworkId)
    {
        // This mapping should be adjusted based on your Network model and N3tdata's network IDs
        $networkMap = [
            1 => 1, // MTN
            2 => 2, // Airtel
            3 => 3, // Glo
            4 => 4, // 9Mobile
        ];

        return $networkMap[$localNetworkId] ?? 1; // Default to MTN if not found
    }

    /**
     * Map subscription plan to N3tdata data plan ID
     * You'll need to configure this based on your subscription plans and N3tdata's data plans
     */
    public function mapDataPlanId($subscriptionPlan)
    {
        // This is a basic mapping - you should create a more sophisticated mapping
        // based on your subscription plans and N3tdata's available data plans

        // Example mapping based on plan names or IDs
        $planMappings = [
            // Format: 'your_plan_identifier' => 'n3tdata_plan_id'
            'basic' => 1,
            'standard' => 2,
            'premium' => 3,
            // You can also map by plan ID
            1 => 1, // Basic plan
            2 => 2, // Standard plan
            3 => 3, // Premium plan
        ];

        // Try to map by plan name first, then by ID
        $planKey = strtolower($subscriptionPlan->name ?? '');
        if (isset($planMappings[$planKey])) {
            return $planMappings[$planKey];
        }

        if (isset($planMappings[$subscriptionPlan->id])) {
            return $planMappings[$subscriptionPlan->id];
        }

        // Default fallback
        return 1;
    }
}
