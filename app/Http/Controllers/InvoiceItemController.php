<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceItemController extends Controller
{
    public function index(Request $request)
    {
        $query = InvoiceItem::with('invoice');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('description', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($q) use ($search) {
                      $q->where('invoice_id', $search);
                  });
        }

        $perPage = $request->input('per_page', 15);
        $invoiceItems = $query->paginate($perPage);

        return response()->json([
            'data'              => $invoiceItems->items(),
            'pagination'        => [
                'total'         => $invoiceItems->total(),
                'per_page'      => $invoiceItems->perPage(),
                'current_page'  => $invoiceItems->currentPage(),
                'last_page'     => $invoiceItems->lastPage(),
                'from'          => $invoiceItems->firstItem(),
                'to'            => $invoiceItems->lastItem()
            ],
            'message'           => 'Invoice Items retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id'    => 'required|exists:invoices,invoice_id',
            'description'   => 'required|string|max:255',
            'amount'        => 'required|numeric|min:0'
        ]);

        $invoiceItem = InvoiceItem::create($validated);

        return response()->json($invoiceItem, 201);
    }

    public function show($id)
    {
        $invoiceItem = InvoiceItem::with('invoice')->findOrFail($id);

        return response()->json($invoiceItem);
    }

    public function update(Request $request, $id)
    {
        $invoiceItem = InvoiceItem::findOrFail($id);

        $validated = $request->validate([
            'invoice_id'    => 'sometimes|required|exists:invoices,invoice_id',
            'description'   => 'sometimes|required|string|max:255',
            'amount'        => 'sometimes|required|numeric|min:0'
        ]);

        $invoiceItem->update($validated);

        return response()->json($invoiceItem);
    }

    public function destroy($id)
    {
        InvoiceItem::findOrFail($id)->delete();

        return response()->noContent();
    }
}
