<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/login
     * Accepts username + password, and optionally an fcm_token.
     * Returns Sanctum token and initializes device connection.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username'  => 'required|string',
            'password'  => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        // 1. Process Device Token (if provided)
        if ($request->has('fcm_token') && $request->fcm_token) {
            $token = $request->fcm_token;

            // 🔥 Ghost Token Cleanup: Find all old tokens and unsubscribe them first
            $oldTokens   = DeviceToken::where('user_id', $user->id)->pluck('fcm_token')->toArray();
            $interestIds = $user->interests()->pluck('interests.id')->toArray();

            if (!empty($oldTokens) && !empty($interestIds)) {
                $fcm = app(\App\Services\FcmDeliveryService::class);
                foreach ($interestIds as $id) {
                    $fcm->unsubscribeFromTopic("interest_{$id}", $oldTokens);
                }
            }

            // Cleanup local database
            DeviceToken::where('user_id', $user->id)->delete();
            DeviceToken::where('fcm_token', $token)->delete();

            // Store the fresh "Only" token
            DeviceToken::create([
                'user_id'   => $user->id,
                'fcm_token' => $token,
            ]);

            // Topic Sync: Subscribe the NEW token
            if (!empty($interestIds)) {
                $fcm = app(\App\Services\FcmDeliveryService::class);
                foreach ($interestIds as $id) {
                    $fcm->subscribeToTopic("interest_{$id}", [$token]);
                }
            }
        }



        // 2. Generate Sanctum token
        $token = $user->createToken('mobile-app')->plainTextToken;

        $user->load('interests:id,name');

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'              => $user->id,
                'username'        => $user->username,
                'name'            => $user->name,
                'profile_details' => $user->profile_details,
                'interests'       => $user->interests,
            ],
            'message' => $request->has('fcm_token') ? 'Login successful and device registered.' : 'Login successful.',
        ]);
    }


    /**
     * GET /api/user
     * Returns the currently authenticated user's profile details and interests.
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $user->load('interests:id,name');

        return response()->json([
            'success'   => true,
            'user'      => [
                'id'              => $user->id,
                'username'        => $user->username,
                'name'            => $user->name,
                'profile_details' => $user->profile_details,
                'interests'       => $user->interests,
            ],
        ]);
    }

    /**
     * POST /api/logout
     * Revokes the current token and removes ALL associated device tokens.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // 🔥 Topic Sync: Unsubscribe all of this user's tokens from their interests before clearing
        $interestIds = $user->interests()->pluck('interests.id')->toArray();
        $tokens      = $user->deviceTokens()->pluck('fcm_token')->toArray();

        if (!empty($interestIds) && !empty($tokens)) {
            $fcm = app(\App\Services\FcmDeliveryService::class);
            foreach ($interestIds as $id) {
                $fcm->unsubscribeFromTopic("interest_{$id}", $tokens);
            }
        }

        // Security: Aggressively remove EVERY device token for this user
        DeviceToken::where('user_id', $user->id)->delete();


        // Revoke the Sanctum access token
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully and all device tokens cleared.'
        ]);
    }


}