<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function getAccessToken(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!auth()->attempt($validated)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = auth()->user()->createToken('authToken')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create($validated);

        return response()->json(['message' => 'User created successfully']);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update($validated);

        return response()->json(['message' => 'User updated successfully']);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
