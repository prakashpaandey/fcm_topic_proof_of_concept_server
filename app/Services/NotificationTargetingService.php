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
     * Targets users by interests, collects tokens, fires notifications.
     */
    public function dispatchForPost(Post $post): void
    {
        // Load the post's tags (interest IDs)
        $tagIds = $post->tags()->pluck('interests.id');

        if ($tagIds->isEmpty()) {
            Log::info("Post #{$post->id} has no tags — skipping notification.");
            return;
        }

        // Find all users whose interests intersect with this post's tags
        // 🔥 CRITICAL: Only target users who have an active login session (Sanctum tokens)
        $targetUsers = \App\Models\User::whereHas('interests', function ($q) use ($tagIds) {
            $q->whereIn('interests.id', $tagIds);
        })
        ->whereHas('tokens') 
        ->where('id', '!=', $post->admin_id)
        ->with('deviceTokens')
        ->get();


        if ($targetUsers->isEmpty()) {
            Log::info("Post #{$post->id}: No matching users found.");
            return;
        }

        // Collect device tokens — deduplicate across users
        $seen = [];

        foreach ($targetUsers as $user) {
            foreach ($user->deviceTokens as $tokenRecord) {
                $token = $tokenRecord->fcm_token;

                // Skip already-seen tokens
                if (in_array($token, $seen)) {
                    continue;
                }
                $seen[] = $token;

                $this->sendAndLog($post, $user, $token);
            }
        }

        Log::info("Post #{$post->id}: Dispatched to " . count($seen) . " device(s).");
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
