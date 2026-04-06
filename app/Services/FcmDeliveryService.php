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
     */
    public function send(string $deviceToken, string $title, string $body, array $data = []): array
    {
        return $this->dispatch(['token' => $deviceToken], $title, $body, $data);
    }

    /**
     * Send a notification to an entire topic
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): array
    {
        // Properly handle /topics/ prefix without using buggy ltrim
        if (str_starts_with($topic, '/topics/')) {
            $topic = substr($topic, 8);
        }
        return $this->dispatch(['topic' => $topic], $title, $body, $data);
    }

    /**
     * Send a notification to a combination of topics using conditions
     * Example: "'interest_1' in topics || 'interest_2' in topics"
     */
    public function sendToCondition(string $condition, string $title, string $body, array $data = []): array
    {
        return $this->dispatch(['condition' => $condition], $title, $body, $data);
    }



    /**
     * Internal dispatcher for FCM v1
     */
    protected function dispatch(array $target, string $title, string $body, array $data): array
    {
        try {
            $accessToken = $this->getAccessToken();
        } catch (Exception $e) {
            Log::error("FCM Token Error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Authentication Failure', 'raw' => $e->getMessage()];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $payload = [
            'message' => array_merge($target, [
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'android' => ['priority' => 'high'],
                'apns'    => ['headers' => ['apns-priority' => '10']],
                'data'    => array_map('strval', $data),
            ]),
        ];

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type'  => 'application/json',
        ])->post($url, $payload);

        return [
            'success' => $response->successful(),
            'error'   => $response->json('error.message'),
            'raw'     => $response->json(),
            'status'  => $response->status(),
        ];
    }

    /**
     * Subscribe tokens to a topic using the Instance ID (IID) API
     */
    public function subscribeToTopic(string $topic, array $tokens): bool
    {
        return $this->manageTopicSubscription('batchAdd', $topic, $tokens);
    }

    /**
     * Unsubscribe tokens from a topic using the IID API
     */
    public function unsubscribeFromTopic(string $topic, array $tokens): bool
    {
        return $this->manageTopicSubscription('batchRemove', $topic, $tokens);
    }

    /**
     * Internal helper for IID API calls
     */
    protected function manageTopicSubscription(string $action, string $topic, array $tokens): bool
    {
        if (empty($tokens)) return true;

        try {
            $accessToken = $this->getAccessToken();
        } catch (Exception $e) {
            Log::error("FCM IID Auth Error: " . $e->getMessage());
            return false;
        }

        // IID API requires the full topic path
        $topicPath = str_starts_with($topic, '/topics/') ? $topic : "/topics/{$topic}";

        $url = "https://iid.googleapis.com/iid/v1:{$action}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$accessToken}",
            'Content-Type'  => 'application/json',
            'access_token_auth' => 'true'
        ])->post($url, [
            'to' => $topicPath,
            'registration_tokens' => $tokens,
        ]);

        if (!$response->successful()) {
            Log::error("FCM Topic {$action} Error: " . $response->body());
        }

        return $response->successful();
    }
}

