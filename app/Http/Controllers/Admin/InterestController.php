<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index()
    {
        $interests = Interest::withCount('users')->latest()->paginate(20);
        return view('admin.interests.index', compact('interests'));
    }

    public function create()
    {
        return view('admin.interests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:interests,name',
        ]);

        Interest::create(['name' => $request->name]);

        return redirect()->route('admin.interests.index')->with('success', 'Interest created successfully.');
    }

    public function edit(Interest $interest)
    {
        return view('admin.interests.edit', compact('interest'));
    }

    public function update(Request $request, Interest $interest)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:interests,name,' . $interest->id,
        ]);

        $interest->update(['name' => $request->name]);

        return redirect()->route('admin.interests.index')->with('success', 'Interest updated.');
    }

    public function destroy(Interest $interest)
    {
        $interest->delete();
        return redirect()->route('admin.interests.index')->with('success', 'Interest deleted.');
    }
}
