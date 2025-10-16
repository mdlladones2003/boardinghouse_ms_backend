<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('room_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 15);
        $rooms = $query->paginate($perPage);

        return response()->json([
            'data'              => $rooms->items(),
            'pagination'        => [
                'total'         => $rooms->total(),
                'per_page'      => $rooms->perPage(),
                'current_page'  => $rooms->currentPage(),
                'last_page'     => $rooms->lastPage(),
                'from'          => $rooms->firstItem(),
                'to'            => $rooms->lastItem()
            ],
            'message'           => 'Rooms retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number'   => 'required|string|unique:rooms,room_number',
            'capacity'      => 'required|integer|min:1',
            'status'        => 'required|in:available,occupied,maintenance,closed',
            'rent_amount'   => 'required|numeric|min:0'
        ]);

        $room = Room::create($validated);

        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);

        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'room_number'   => 'sometimes|required|string|unique:rooms,room_number',
            'capacity'      => 'sometimes|required|integer|min:1',
            'status'        => 'sometimes|required|in:available,occupied,maintenance,closed',
            'rent_amount'   => 'sometimes|required|numeric|min:0'
        ]);

        $room->update($validated);

        return response()->json($room);
    }

    public function destroy($id)
    {
        Room::findOrFail($id)->delete();

        return response()->noContent();
    }
}
