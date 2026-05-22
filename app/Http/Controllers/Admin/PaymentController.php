<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with('user', 'subscription')->latest();

        // Search
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('transaction_id', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status') && $request->filled('status')) {
            $query->where('status', strtoupper($request->input('status')));
        }

        $payments = $query->paginate(15);

        return view('admin.payments.index', ['payments' => $payments]);
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment);
        return view('admin.payments.show', ['payment' => $payment->load('user', 'subscription')]);
    }
}
