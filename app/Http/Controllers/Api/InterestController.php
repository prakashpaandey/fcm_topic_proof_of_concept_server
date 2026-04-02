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
     * Syncs the authenticated user's interest selections.
     * The mobile app sends the full updated list of interest IDs.
     */
    public function sync(Request $request)
    {
        $request->validate([
            'interest_ids'   => 'required|array',
            'interest_ids.*' => 'integer|exists:interests,id',
        ]);

        // Sync replaces existing selections dynamically
        $request->user()->interests()->sync($request->interest_ids);

        $updated = $request->user()->interests()->get(['interests.id', 'name']);

        return response()->json([
            'success'   => true,
            'message'   => 'Interests updated successfully.',
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
