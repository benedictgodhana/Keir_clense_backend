<?php

namespace App\Http\Controllers;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'employee'])
            ->get()
            ->map(function ($review) {
                $employeeName = User::find($review->employee_id)->name;
                $customerEmail = $review->user->email;
                $employeeEmail = User::find($review->employee_id)->email;


                return [
                    'CustomerName' => $review->user->name,
                    'EmployeeName' => $employeeName,
                    'CustomerEmail' => $customerEmail,
                    'EmployeeEmail' => $employeeEmail,
                    'Rating' => $review->rating,
                    'Comment' => $review->comment,
                    'created_at' => $review->created_at,
                    'updated_at' => $review->updated_at,
                ];
            });

        return response()->json(['reviews' => $reviews], 200);
    }
}

