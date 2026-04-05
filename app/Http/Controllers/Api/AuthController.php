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
            // Delete all previous tokens for this user first
            DeviceToken::where('user_id', $user->id)->delete();
            
            // Delete this token if previously owned by another user
            DeviceToken::where('fcm_token', $request->fcm_token)->delete();

            // Store the fresh "Only" token
            DeviceToken::create([
                'user_id'   => $user->id,
                'fcm_token' => $request->fcm_token,
            ]);
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

        // Security: Aggressively remove EVERY device token for this user
        // This ensures they stop receiving notifications on EVERY phone immediately.
        DeviceToken::where('user_id', $user->id)->delete();

        // Revoke the Sanctum access token
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully and all device tokens cleared.'
        ]);
    }


}
