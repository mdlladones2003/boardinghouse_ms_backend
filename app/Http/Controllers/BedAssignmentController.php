<?php

namespace App\Http\Controllers;

use App\Models\BedAssignment;
use Illuminate\Http\Request;

class BedAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = BedAssignment::with(['room', 'tenant']);

        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->whereHas('room', function ($q2) use ($search) {
                    $q2->where('room_number', 'like', "%{$search}%");
                })
                ->orWhereHas('tenant', function ($q3) use ($search) {
                    $q3->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $bedAssignments = $query->paginate($perPage);

        return response()->json([
            'data'              => $bedAssignments->items(),
            'pagination'        => [
                'total'         => $bedAssignments->total(),
                'per_page'      => $bedAssignments->perPage(),
                'current_page'  => $bedAssignments->currentPage(),
                'last_page'     => $bedAssignments->lastPage(),
                'from'          => $bedAssignments->firstItem(),
                'to'            => $bedAssignments->lastItem()
            ],
            'message'           => 'Bed Assignments retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id'       => 'required|exists:rooms,room_id',
            'tenant_id'     => 'required|exists:tenants,tenant_id',
            'bed_number'    => 'required|integer|min:1',
            'assigned_on'   => 'required|date',
            'status'        => 'required|in:assigned,vacant'
        ]);

        $bedAssignment = BedAssignment::create($validated);

        return response()->json($bedAssignment, 201);
    }

    public function show($id)
    {
        $bedAssignment = BedAssignment::with(['room', 'tenant'])->findOrFail($id);

        return response()->json($bedAssignment);
    }

    public function update(Request $request, $id)
    {
        $bedAssignment = BedAssignment::findOrFail($id);

        $validated = $request->validate([
            'room_id'       => 'sometimes|required|exists:rooms,room_id',
            'tenant_id'     => 'sometimes|required|exists:tenants,tenant_id',
            'bed_number'    => 'sometimes|required|integer|min:1',
            'assigned_on'   => 'sometimes|required|date',
            'status'        => 'sometimes|required|in:assigned,vacant'
        ]);

        $bedAssignment->update($validated);

        return response()->json($bedAssignment);
    }

    public function destroy($id)
    {
        BedAssignment::findOrFail($id)->delete();

        return response()->noContent();
    }
}
