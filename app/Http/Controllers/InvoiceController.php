<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['tenant', 'invoiceItems']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('tenant', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })
            ->orWhere('status', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 15);
        $invoices = $query->paginate($perPage);

        return response()->json([
            'data'              => $invoices->items(),
            'pagination'        => [
                'total'         => $invoices->total(),
                'per_page'      => $invoices->perPage(),
                'current_page'  => $invoices->currentPage(),
                'last_page'     => $invoices->lastPage(),
                'from'          => $invoices->firstItem(),
                'to'            => $invoices->lastItem()
            ],
            'message'           => 'Invoices retrieved successfully'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id'     => 'required|exists:tenants,tenant_id',
            'issue_date'    => 'required|date',
            'due_date'      => 'required|date|after_or_equal:issue_date',
            'total_amount'  => 'required|numeric|min:0',
            'status'        => 'required|in:pending,paid,cancelled'
        ]);

        $invoice = Invoice::create($validated);

        return response()->json([
            'data'      => $invoice,
            'message'   => 'Invoice created successfully'
        ], 201);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['tenant', 'invoiceItems'])->findOrFail($id);

        return response()->json([
            'data'      => $invoice,
            'message'   => 'Invoice retrieved successfully'
        ]);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'tenant_id'     => 'sometimes|required|exists:tenants,tenant_id',
            'issue_date'    => 'sometimes|required|date',
            'due_date'      => 'sometimes|required|date|after_or_equal:issue_date',
            'total_amount'  => 'sometimes|required|numeric|min:0',
            'status'        => 'sometimes|required|in:pending,paid,cancelled'
        ]);

        $invoice->update($validated);

        return response()->json([
            'data'      => $invoice,
            'message'   => 'Invoice updated successfully'
        ]);
    }

    public function destroy($id)
    {
        Invoice::findOrFail($id)->delete();

        return response()->noContent();
    }
}
