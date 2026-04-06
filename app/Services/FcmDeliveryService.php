<?php

namespace App\Services;

use Exception;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmDeliveryService
{
    protected string $projectId;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID', '');
        $this->credentialsPath = base_path(env('FIREBASE_CREDENTIALS', 'storage/firebase/service-account.json'));
    }

    /**
     * Get OAuth2 Token securely using Google API Client
     */
    protected function getAccessToken(): string
    {
        $client = new GoogleClient();
        $client->setAuthConfig($this->credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();

        $token = $client->getAccessToken();

        if (!isset($token['access_token'])) {
            throw new Exception("Failed to retrieve FCM access token.");
        }

        return $token['access_token'];
    }

    /**
     * Send a single push notification through Firebase HTTP v1 API
     * @param string $priority 'HIGH' for high priority notifications, 'NORMAL' for low priority (default: 'HIGH')
     */
    public function send(string $deviceToken, string $title, string $body, array $data = [], string $priority = 'HIGH'): array
    {
        try {
            $accessToken = $this->getAccessToken();
        } catch (Exception $e) {
            Log::error("FCM Token Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Authentication Failure',
                'raw' => $e->getMessage(),
            ];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                // 🔥 High Priority Configuration
                'android' => [
                    'priority' => 'high',
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10', // 10 = Immediate delivery
                    ],
                ],
                'data' => array_map('strval', $data), // Data mapping requires string values in FCM v1
            ],
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type'  => 'application/json',
        ])->post($url, $payload);

        if ($response->successful()) {
            return [
                'success' => true,
                'error'   => null,
                'raw'     => $response->json(),
            ];
        }

        return [
            'success' => false,
            'error'   => $response->json('error.message', 'Unknown Error'),
            'raw'     => $response->json(),
            'status'  => $response->status(),
        ];
    }
}
