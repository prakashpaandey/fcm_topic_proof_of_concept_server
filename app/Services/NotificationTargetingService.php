<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\NotificationLog;
use App\Models\Post;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationTargetingService
{
    public function __construct(protected FcmDeliveryService $fcm) {}

    /**
     * Main entry point — called right after a Post is created.
     * Uses High-Performance Topic Dispatching with Deduplication.
     */
    public function dispatchForPost(Post $post): void
    {
        // 1. Load the post's tags (Interest IDs)
        $tagIds = $post->tags()->pluck('interests.id')->unique();

        if ($tagIds->isEmpty()) {
            Log::info("Notification Skipped: Post #{$post->id} ('{$post->title}') has no interests/tags attached.");
            return;
        }


        $fcm = app(\App\Services\FcmDeliveryService::class);
        $title = $post->title;
        $body  = substr(strip_tags($post->text_content), 0, 150);
        $data  = [
            'post_id'   => (string) $post->id,
            'author_id' => (string) $post->admin_id,
        ];

        // 2. Build Targeting (Single Topic vs. Combined Condition)
        if ($tagIds->count() === 1) {
            $target = "interest_" . $tagIds->first();
            $result = $fcm->sendToTopic($target, $title, $body, $data);
            $logTarget = $target;
        } else {
            // Firebase allows up to 5 topics in a condition.
            // We join them with OR (||) to reach anyone who matches at least one tag.
            $topics = $tagIds->take(5)->map(fn($id) => "'interest_{$id}' in topics");
            $condition = $topics->implode(' || ');
            
            $result = $fcm->sendToCondition($condition, $title, $body, $data);
            $logTarget = "Condition: " . $condition;
        }

        // 3. Log the dispatch result
        if ($result['success']) {
            Log::info("Post #{$post->id}: Successfully dispatched to {$logTarget}.");
        } else {
            Log::error("Post #{$post->id}: Failed to dispatch to {$logTarget}. Error: " . ($result['error'] ?? 'Unknown'));
        }

        NotificationLog::create([
            'post_id'       => $post->id,
            'user_id'       => null,
            'fcm_token'     => substr($logTarget, 0, 255), // Store the target description
            'status'        => $result['success'] ? 'success' : 'failed',
            'error_message' => $result['error'] ?? null,
        ]);
    }



    /**
     * Send notification to a single token and log the result.
     */
    protected function sendAndLog(Post $post, $user, string $token): void
    {
        $title   = $post->title;
        $body    = substr(strip_tags($post->text_content), 0, 150);
        $data    = ['post_id' => (string) $post->id];

        $result = $this->fcm->send($token, $title, $body, $data);

        NotificationLog::create([
            'post_id'       => $post->id,
            'user_id'       => $user->id,
            'fcm_token'     => $token,
            'status'        => $result['success'] ? 'success' : 'failed',
            'error_message' => $result['error'] ?? null,
        ]);

        // If token is invalid/unregistered — remove it from DB
        if (!$result['success']) {
            $errorMsg = strtolower($result['error'] ?? '');
            if (
                str_contains($errorMsg, 'not found') ||
                str_contains($errorMsg, 'unregistered') ||
                str_contains($errorMsg, 'invalid')
            ) {
                DeviceToken::where('fcm_token', $token)->delete();
                Log::warning("Removed invalid FCM token: {$token}");
            }
        }
    }
}
