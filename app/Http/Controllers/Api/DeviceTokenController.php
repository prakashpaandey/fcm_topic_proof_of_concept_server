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

        // Check if this token already exists in the system
        $existing = DeviceToken::where('fcm_token', $token)->first();

        if ($existing) {
            // If token belongs to another user, reassign it
            if ($existing->user_id !== $user->id) {
                $existing->update(['user_id' => $user->id]);
            }
            // If already assigned to current user — no-op
        } else {
            // Register fresh token
            DeviceToken::create([
                'user_id'   => $user->id,
                'fcm_token' => $token,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Device token registered successfully.',
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
