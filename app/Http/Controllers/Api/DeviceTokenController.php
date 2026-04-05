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

        // Security: Remove ALL other tokens for this user first
        // This ensures the user only ever has ONE active device in the system at a time
        // and prevents "double notifications" if their token changes.
        DeviceToken::where('user_id', $user->id)->delete();

        // Also remove this token if it was previously assigned to someone else
        DeviceToken::where('fcm_token', $token)->delete();

        // Register the fresh token as the ONLY one for this user
        DeviceToken::create([
            'user_id'   => $user->id,
            'fcm_token' => $token,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device token updated and old tokens cleared.',
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
