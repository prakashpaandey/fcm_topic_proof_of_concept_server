<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    /**
     * GET /api/interests
     * Returns the full list of interests for mobile app to display.
     */
    public function index()
    {
        $interests = Interest::orderBy('name')->get(['id', 'name']);

        return response()->json([
            'success'   => true,
            'interests' => $interests,
        ]);
    }

    /**
     * POST /api/user/interests
     * Syncs the authenticated user's interest selections and manages FCM topics.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'interest_ids'   => 'required|array',
            'interest_ids.*' => 'integer|exists:interests,id',
        ]);

        $user = $request->user();
        
        // 1. Get current IDs before sync
        $oldIds = $user->interests()->pluck('interests.id')->toArray();
        $newIds = $request->interest_ids;

        // 2. Perform the database sync
        $user->interests()->sync($newIds);

        // 3. Calculate ADDED and REMOVED IDs
        $addedIds   = array_diff($newIds, $oldIds);
        $removedIds = array_diff($oldIds, $newIds);

        // 4. Get all user tokens for subscription management
        $tokens = $user->deviceTokens()->pluck('fcm_token')->toArray();

        if (!empty($tokens)) {
            $fcm = app(\App\Services\FcmDeliveryService::class);
            
            foreach ($addedIds as $id) {
                $fcm->subscribeToTopic("interest_{$id}", $tokens);
            }
            foreach ($removedIds as $id) {
                $fcm->unsubscribeFromTopic("interest_{$id}", $tokens);
            }
        }

        $updated = $user->interests()->get(['interests.id', 'name']);

        return response()->json([
            'success'   => true,
            'message'   => 'Interests updated and topics synchronized.',
            'interests' => $updated,
        ]);
    }


    /**
     * GET /api/user/interests
     * Returns the current user's selected interests.
     */
    public function userInterests(Request $request)
    {
        $interests = $request->user()->interests()->get(['interests.id', 'name']);

        return response()->json([
            'success'   => true,
            'interests' => $interests,
        ]);
    }
}
