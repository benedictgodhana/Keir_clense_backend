<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{


public function store(Request $request)
{
    // Check if the user is authenticated
    if (!Auth::check()) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $validatedData = $request->validate([
        'date' => 'required|date',
        'time' => 'required',
        'location' => 'required',
        'selectedProvider' => 'required',
        // Add more validation rules as needed
    ]);

    try {
        // Get the authenticated user
        $user = Auth::user();

        // Create a new booking record associated with the authenticated user
        $booking = Booking::create([
            'date' => $validatedData['date'],
            'time' => $validatedData['time'],
            'location' => $validatedData['location'],
            'user_id' => $user->id, // Assign the user ID to the booking
            // Add more fields as needed
        ]);

        // Return a success response
        return response()->json(['message' => 'Booking created successfully', 'booking' => $booking], 201);
    } catch (\Exception $e) {
        // Handle any exceptions, such as database errors
        return response()->json(['error' => 'Failed to create booking', 'message' => $e->getMessage()], 500);
    }
}


    public function getAvailableEmployees(Request $request)
{
    // Validate the incoming request (optional)
    $request->validate([
        'service' => 'required|exists:services,name', // Ensure the selected service exists
    ]);

    // Retrieve the selected service
    $selectedService = $request->input('service');

    // Query the database to find available employees for the selected service
    $availableEmployees = Employee::whereHas('service', function ($query) use ($selectedService) {
        $query->where('name', $selectedService);
    })->with('user')->get();

    // Extract user names from available employees
    $userNames = $availableEmployees->pluck('user.name');

    // Return the list of available employee names
    return response()->json($userNames);
}
public function getEmployeeBookings()
{
    // Check if there is an authenticated user
    if (Auth::check()) {
        // Retrieve the authenticated user
        $user = Auth::user();

        // Check if the user has the role of "employee"
        if ($user->hasRole('employee')) {
            // If the user has the role, fetch the associated employee record
            $employee = $user->employee;

            // Check if the employee record exists
            if ($employee) {
                // Fetch bookings assigned to the employee
                $bookings = $employee->bookings;

                // Return the bookings as JSON response
                return response()->json($bookings);
            } else {
                // If the employee record does not exist, return an error response
                return response()->json(['error' => 'Employee record not found'], 404);
            }
        } else {
            // If the user does not have the role of "employee", return an error response
            return response()->json(['error' => 'User does not have the role of employee'], 403);
        }
    } else {
        // If there is no authenticated user, return an error response
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}

}


