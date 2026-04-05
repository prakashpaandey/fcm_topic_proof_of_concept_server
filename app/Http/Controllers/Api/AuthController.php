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
     * Accepts username + password, returns Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke old tokens (optional — keeps only fresh login)
        // $user->tokens()->delete();

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
     * Revokes the current token and optionally removes the associated device token.
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // If the client provides an fcm_token, remove it from our database
        // so that notifications stop immediately.
        if ($request->has('fcm_token')) {
            DeviceToken::where('user_id', $user->id)
                ->where('fcm_token', $request->fcm_token)
                ->delete();
        }

        // Revoke the Sanctum access token
        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully and device token removed.'
        ]);
    }

}
