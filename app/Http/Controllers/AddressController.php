<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index(Request $request)
    {
        $query = Address::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('city', 'like', "%{$search}%")
                  ->orWhere('country', 'like', "%{$search}%");
        }

        $addresses = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'data'              => $addresses->items(),
            'pagination'        => [
                'total'         => $addresses->total(),
                'per_page'      => $addresses->perPage(),
                'current_page'  => $addresses->currentPage(),
                'last_page'     => $addresses->lastPage(),
                'from'          => $addresses->firstItem(),
                'to'            => $addresses->lastItem()
            ],
            'message'           => 'Addresses retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'street_address'    => 'required|string|max:255',
            'city'              => 'required|string|max:100',
            'state'             => 'required|string|max:100',
            'postal_code'       => 'required|string|max:20',
            'country'           => 'required|string|max:100'
        ]);

        $address = Address::create($validated);

        return response()->json($address, 201);
    }

    public function show($id)
    {
        $address = Address::findOrFail($id);

        return response()->json($address);
    }

    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        $validated = $request->validate([
            'street_address'    => 'sometimes|required|string|max:255',
            'city'              => 'sometimes|required|string|max:100',
            'state'             => 'sometimes|required|string|max:100',
            'postal_code'       => 'sometimes|required|string|max:20',
            'country'           => 'sometimes|required|string|max:100'
        ]);

        $address->update($validated);

        return response()->json($address);
    }

    public function destroy($id)
    {
        Address::findOrFail($id)->delete();

        return response()->noContent();
    }
}
