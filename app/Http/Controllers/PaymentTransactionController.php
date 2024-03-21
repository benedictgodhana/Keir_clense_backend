<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentTransaction;

class PaymentTransactionController extends Controller
{
    public function index()
    {
        // Load payment transactions with related models (if relationships are defined)
        $transactions = PaymentTransaction::with('booking')->get();

        // Transform the data to include names instead of IDs
        $transformedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'booking_id' => $transaction->booking->location, // Assuming 'name' is the attribute you want to display
                'amount' => $transaction->amount,
                'payment_method' => $transaction->payment_method,
                'status' => $transaction->status,
                'transaction_id' => $transaction->transaction_id,
                'created_at' => $transaction->created_at,
                'updated_at' => $transaction->updated_at,
            ];
        });

        // Return the transformed data as JSON response
        return response()->json(['transactions' => $transformedTransactions], 200);
    }
}
