<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with(['room', 'address']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = $request->input('per_page', 15);
        $tenants = $query->paginate($perPage);

        return response()->json([
            'data'              => $tenants->items(),
            'pagination'        => [
                'total'         => $tenants->total(),
                'per_page'      => $tenants->perPage(),
                'current_page'  => $tenants->currentPage(),
                'last_page'     => $tenants->lastPage(),
                'from'          => $tenants->firstItem(),
                'to'            => $tenants->lastItem()
            ],
            'message' => 'Tenants retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'       => 'nullable|exists:rooms,room_id',
            'address_id'    => 'nullable|exists:addresses,address_id',
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'required|string|max:100',
            'phone'         => 'required|string|max:20',
            'move_in'       => 'required|date',
            'status'        => 'required|in:active,inactive,evicted'
        ]);

        $tenant = Tenant::create($validated);

        return response()->json($tenant, 201);
    }

    public function show($id)
    {
        $tenant = Tenant::with(['room', 'address', 'bedAssignments', 'invoices', 'payments'])->findOrFail($id);

        return response()->json($tenant);
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $validated = $request->validate([
            'room_id'       => 'sometimes|nullable|exists:rooms,room_id',
            'address_id'    => 'sometimes|nullable|exists:addresses,address_id',
            'first_name'    => 'sometimes|required|string|max:100',
            'last_name'     => 'sometimes|required|string|max:100',
            'phone'         => 'sometimes|required|string|max:20',
            'move_in'       => 'sometimes|required|date',
            'status'        => 'sometimes|required|in:active,inactive,evicted'
        ]);

        $tenant->update($validated);

        return response()->json($tenant);
    }

    public function destroy($id)
    {
        Tenant::findOrFail($id)->delete();

        return response()->noContent();
    }
}
