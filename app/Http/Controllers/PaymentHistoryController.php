<?php

namespace App\Http\Controllers;

use App\Models\PaymentHistory;
use Illuminate\Http\Request;

class PaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentHistory::with(['payment', 'user']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('action', 'like', "%{$search}%")
                  ->orWhereHas('payment', function ($q) use ($search) {
                      $q->where('payment_id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        $perPage = $request->input('per_page', 15);
        $histories = $query->paginate($perPage);

        return response()->json([
            'data'              => $histories->items(),
            'pagination'        => [
                'total'         => $histories->total(),
                'per_page'      => $histories->perPage(),
                'current_page'  => $histories->currentPage(),
                'last_page'     => $histories->lastPage(),
                'from'          => $histories->firstItem(),
                'to'            => $histories->lastItem()
            ],
            'message'           => 'Payment Histories retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id'    => 'required|exists:payments,payment_id',
            'user_id'       => 'required|exists:users,user_id',
            'action'        => 'required|string|max:255',
            'action_type'   => 'required|in:created,updated,deleted',
            'action_date'   => 'required|date'
        ]);

        $history = PaymentHistory::create($validated);

        return response()->json($history, 201);
    }

    public function show($id)
    {
        $history = PaymentHistory::with(['payment', 'user'])->findOrFail($id);

        return response()->json($history);
    }

    public function update(Request $request, $id)
    {
        $history = PaymentHistory::findOrFail($id);

        $validated = $request->validate([
            'payment_id'    => 'sometimes|required|exists:payments,payment_id',
            'user_id'       => 'sometimes|required|exists:users,user_id',
            'action'        => 'sometimes|required|string|max:255',
            'action_type'   => 'sometimes|required|in:created,updated,deleted',
            'action_date'   => 'sometimes|required|date'
        ]);

        $history->update($validated);

        return response()->json($history);
    }

    public function destroy($id)
    {
        PaymentHistory::findOrFail($id)->delete();

        return response()->noContent();
    }
}
