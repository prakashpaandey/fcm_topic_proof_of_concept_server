<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class DeviceTokenController extends Controller
{
    /**
     * POST /api/device-tokens
     * Registers or updates the FCM token for the authenticated user's device.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $token = $request->fcm_token;
        $user  = $request->user();

        // 1. 🔥 Ghost Token Cleanup: Unsubscribe old tokens from all topics before deleting them
        $oldTokens   = DeviceToken::where('user_id', $user->id)->pluck('fcm_token')->toArray();
        $interestIds = $user->interests()->pluck('interests.id')->toArray();

        if (!empty($oldTokens) && !empty($interestIds)) {
            $fcm = app(\App\Services\FcmDeliveryService::class);
            foreach ($interestIds as $id) {
                // Remove all old tokens from this topic
                $fcm->unsubscribeFromTopic("interest_{$id}", $oldTokens);
            }
        }

        // 2. Local Database Sync: Remove entries for this user and this token
        DeviceToken::where('user_id', $user->id)->delete();
        DeviceToken::where('fcm_token', $token)->delete();

        // 3. Register the fresh token as the ONLY one for this user
        DeviceToken::create([
            'user_id'   => $user->id,
            'fcm_token' => $token,
        ]);

        // 4. Topic Sync: Subscribe the NEW token
        if (!empty($interestIds)) {
            $fcm = app(\App\Services\FcmDeliveryService::class);
            foreach ($interestIds as $id) {
                $fcm->subscribeToTopic("interest_{$id}", [$token]);
            }
        }


        return response()->json([
            'success' => true,
            'message' => 'Device token updated and topics synchronized.',
        ]);
    }



    /**
     * DELETE /api/device-tokens
     * Removes a specific FCM token (e.g. on user logout from a device).
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        DeviceToken::where('user_id', $request->user()->id)
            ->where('fcm_token', $request->fcm_token)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Device token removed.',
        ]);
    }
}
