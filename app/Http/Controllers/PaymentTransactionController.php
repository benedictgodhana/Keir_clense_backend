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


    public function update($id, Request $request)
    {
        // Find the payment transaction by its ID
        $paymentTransaction = PaymentTransaction::find($id);

        if (!$paymentTransaction) {
            // If the payment transaction is not found, return a 404 response
            return response()->json(['error' => 'Payment transaction not found'], 404);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'status' => 'required|in:Pending,Confirmed,Failed',
            'transaction_id' => 'required|string|max:255',
            // Add validation rules for other fields if needed
        ]);

        // Update the payment transaction with the validated data
        $paymentTransaction->update($validatedData);

        // Return a success response
        return response()->json(['message' => 'Payment transaction updated successfully', 'paymentTransaction' => $paymentTransaction]);
    }
}
