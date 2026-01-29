<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff.
     */
    public function index()
    {
        return response()->json(User::all());
    }

    /**
     * Store a newly created staff in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'position' => 'nullable|string',
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_STAFF])],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $staff = User::create($validated);

        return response()->json([
            'message' => 'Staff member created successfully',
            'data' => $staff
        ], 201);
    }

    /**
     * Display the specified staff.
     */
    public function show(User $staff)
    {
        return response()->json($staff);
    }

    /**
     * Update the specified staff in storage.
     */
    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'password' => 'sometimes|required|string|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'position' => 'nullable|string',
            'status' => 'sometimes|boolean',
            'role' => ['sometimes', 'required', Rule::in([User::ROLE_ADMIN, User::ROLE_STAFF])],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $staff->update($validated);

        return response()->json([
            'message' => 'Staff member updated successfully',
            'data' => $staff
        ]);
    }

    /**
     * Remove the specified staff from storage.
     */
    public function destroy(User $staff)
    {
        if ($staff->isRoot()) {
            return response()->json(['message' => 'Cannot delete root user'], 403);
        }

        $staff->delete();

        return response()->json(['message' => 'Staff member deleted successfully']);
    }
}
