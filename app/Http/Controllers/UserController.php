<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List all users (admin only).
     */
    public function index(): JsonResponse
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    /**
     * Create a new user (admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(['admin', 'user'])],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => $validated['password'], // cast in model
            'role'     => $validated['role'],
        ]);

        return response()->json([
            'message' => 'Pengguna berhasil dibuat.',
            'user'    => $user->only(['id', 'name', 'email', 'role', 'created_at']),
        ], 201);
    }

    /**
     * Show a specific user (admin only).
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($user->only(['id', 'name', 'email', 'role', 'created_at']));
    }

    /**
     * Update an existing user (admin only).
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'sometimes|nullable|string|min:8',
            'role'     => ['sometimes', Rule::in(['admin', 'user'])],
        ]);

        if (isset($validated['password']) && $validated['password']) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui.',
            'user'    => $user->fresh()->only(['id', 'name', 'email', 'role', 'created_at']),
        ]);
    }

    /**
     * Delete a user (admin only).
     */
    public function destroy(User $user): JsonResponse
    {
        // Prevent self-deletion
        if (auth()->id() === $user->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun Anda sendiri.'], 403);
        }

        $user->tokens()->delete();
        $user->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus.']);
    }
}
