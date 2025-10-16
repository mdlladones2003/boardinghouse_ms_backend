<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('tenant');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
        }

        $perPage = $request->input('per_page', 15);
        $users = $query->paginate($perPage);

        return response()->json([
            'data'              => $users->items(),
            'pagination'        => [
                'total'         => $users->total(),
                'per_page'      => $users->perPage(),
                'current_page'  => $users->currentPage(),
                'last_page'     => $users->lastPage(),
                'from'          => $users->firstItem(),
                'to'            => $users->lastItem()
            ],
            'message'           => 'Users retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id'     => 'nullable|exists:tenants,tenant_id',
            'username'      => 'required|string|unique:users,username,max:50',
            'password'      => 'required|string|min:8',
            'email'         => 'required|string|email|unique:users,email',
            'role'          => 'required|in:tenant,admin,staff'
        ]);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::with('tenant')->findOrFail($id);

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'tenant_id'     => 'nullable|exists:tenants,tenant_id',
            'username'      => 'sometimes|required|string|unique:users,username,' . $user->user_id . ',user_id|max:50',
            'password'      => 'sometimes|required|string|min:8',
            'email'         => 'sometimes|required|string|email|unique:users,email,' . $user->user_id . ',user_id',
            'role'          => 'sometimes|required|in:tenant,admin,staff'
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->noContent();
    }
}
