<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('tenant');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('payment_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('tenant', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
        }

        $perPage = $request->input('per_page', 15);
        $payments = $query->paginate($perPage);

        return response()->json([
            'data'              => $payments->items(),
            'pagination'        => [
                'total'         => $payments->total(),
                'per_page'      => $payments->perPage(),
                'current_page'  => $payments->currentPage(),
                'last_page'     => $payments->lastPage(),
                'from'          => $payments->firstItem(),
                'to'            => $payments->lastItem()
            ],
            'message' => 'Payments retreived successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id'     => 'required|exists:tenants,tenant_id',
            'payment_date'  => 'required|date',
            'amount'        => 'required|numeric|min:0',
            'payment_type'  => 'required|in:cash,credit_card,bank_transfer,online',
            'status'        => 'required|in:completed,pending,failed'
        ]);

        $payment = Payment::create($validated);

        return response()->json($payment, 201);
    }

    public function show($id)
    {
        $payment = Payment::with('tenant')->findOrFail($id);

        return response()->json($payment);
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $validated = $request->validate([
            'tenant_id'     => 'sometimes|required|exists:tenants,tenant_id',
            'payment_date'  => 'sometimes|required|date',
            'amount'        => 'sometimes|required|numeric|min:0',
            'payment_type'  => 'sometimes|required|in:cash,credit_card,bank_transfer,online',
            'status'        => 'sometimes|required|in:completed,pending,failed'
        ]);

        $payment->update($validated);

        return response()->json($payment);
    }

    public function destroy($id)
    {
        Payment::findOrFail($id)->delete();

        return response()->noContent();
    }
}
