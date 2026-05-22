<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use PDF;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('payment.user')->latest()->paginate(15);
        return view('admin.invoices.index', ['invoices' => $invoices]);
    }

    public function create()
    {
        $payments = Payment::with('user', 'subscription')->where('status', 'completed')->get();
        return view('admin.invoices.create', ['payments' => $payments]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id|unique:invoices',
            'invoice_number' => 'required|string|unique:invoices',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        $payment = Payment::find($validated['payment_id']);

        $taxAmount = ($payment->amount * ($validated['tax_rate'] ?? 0)) / 100;
        $totalAmount = $payment->amount + $taxAmount;

        $validated['total_amount'] = $totalAmount;
        $validated['tax_amount'] = $taxAmount;
        $validated['issued_at'] = now();
        $validated['due_at'] = now()->addDays(30);

        Invoice::create($validated);

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load('payment.user', 'payment.subscription');
        return view('admin.invoices.show', ['invoice' => $invoice]);
    }

    public function download(Invoice $invoice)
    {
        // Generate PDF (requires barryvdh/laravel-dompdf or similar)
        // For now, return a view
        return view('admin.invoices.pdf', ['invoice' => $invoice->load('payment.user', 'payment.subscription')]);
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully');
    }
}
