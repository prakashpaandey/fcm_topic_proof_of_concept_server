<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['interests', 'deviceTokens'])->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username'        => 'required|string|max:50|unique:users,username',
            'name'            => 'nullable|string|max:100',
            'profile_details' => 'nullable|string',
            'password'        => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'username'        => $request->username,
            'name'            => $request->name,
            'profile_details' => $request->profile_details,
            'password'        => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username'        => 'required|string|max:50|unique:users,username,' . $user->id,
            'name'            => 'nullable|string|max:100',
            'profile_details' => 'nullable|string',
            'password'        => 'nullable|string|min:6|confirmed',
        ]);

        $data = [
            'username'        => $request->username,
            'name'            => $request->name,
            'profile_details' => $request->profile_details,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
