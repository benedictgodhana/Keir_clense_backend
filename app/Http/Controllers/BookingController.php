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
       // Validate the incoming request data
       $validatedData = $request->validate([
        'service_id' => 'required',
        'user_id'=>'required',
        'employee_id' => 'required',
        'date_time' => 'required|date',
        'location' => 'required',
        // Add more validation rules as needed
    ]);



    try {

        // Create a new booking record
        $booking = Booking::create([
            'user_id' => $validatedData['user_id'], // Assuming the user is authenticated
            'service_id' => $validatedData['service_id'],
            'employee_id' => $validatedData['employee_id'],
            'date_time' => $validatedData['date_time'],
            'location' => $validatedData['location'],
            'status' => 'pending', // Assuming the initial status is 'pending'
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
    })->with('user', 'service:id,name')->get();

    // Extract employee names, IDs, and service name
    $employeeData = $availableEmployees->map(function ($employee) use ($selectedService) {
        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->user->name,
            'service_id' => $employee->service->id,
            'service_name' => $selectedService,
        ];
    });

    // Return the list of available employees with names, IDs, and service name
    return response()->json($employeeData);
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
public function fetchBookings()
{
    // Fetch all bookings with related models and their status
    $bookings = Booking::with('user', 'service', 'employee')->get();

    // Transform the bookings data to include status names instead of IDs
    $formattedBookings = $bookings->map(function ($booking) {
        // Retrieve employee name based on employee_id
        $employeeName = User::find($booking->employee_id)->name;

        return [
            'customerName' => $booking->user->name,
            'employeeName' => $employeeName,
            'serviceName' => $booking->service->name,
            'status' => $booking->status, // Assuming the status name is accessible through the relationship
            'location' => $booking->location,
            // Add more fields as needed
        ];
    });

    // Return the formatted bookings as JSON response
    return response()->json($formattedBookings);
}



}


