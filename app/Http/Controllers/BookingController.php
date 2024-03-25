<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Employee;
use App\Models\PaymentTransaction;
use App\Notifications\BookingNotificationForUser;
use App\Notifications\BookingNotificationForEmployee;
use App\Notifications\BookingNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'service_id' => 'required',
            'user_id' => 'required',
            'employee_id' => 'required',
            'date_time' => 'required|date',
            'location' => 'required',
            'contact' => 'required', // Add validation for contact
            'payment_method' => 'required',
            'comments' => 'nullable|string|max:255', // Validation for comments, allowing it to be nullable
        ]);

        // Check if the selected employee is already booked at the specified date and time
        $existingBooking = Booking::where('employee_id', $validatedData['employee_id'])
                                    ->where('date_time', $validatedData['date_time'])
                                    ->first();

        if ($existingBooking) {
            // If an existing booking is found, return an error response
            return response()->json(['error' => 'Employee is already booked at this time. Please select another time.'], 400);
        }

        try {
            // Create a new booking record
            $booking = Booking::create([
                'user_id' => $validatedData['user_id'],
                'service_id' => $validatedData['service_id'],
                'employee_id' => $validatedData['employee_id'],
                'date_time' => $validatedData['date_time'],
                'location' => $validatedData['location'],
                'contact' => $validatedData['contact'], // Assign the contact
                'payment_method' => $validatedData['payment_method'], // Assign the payment method
                'status' => 'pending',
                'comments' => $validatedData['comments'] ?? null, // Assign the comments, if provided
                // Add more fields as needed
            ]);

            // Create a new payment transaction record
            $payment = PaymentTransaction::create([
                'booking_id' => $booking->id,
                'amount' => $booking->service->price, // Assuming the service price is stored in the service table
                'payment_method' => $validatedData['payment_method'],
                'status' => 'pending', // Initial status of payment transaction
                // Add more fields as needed
            ]);

            // Notify the employee about the booking
            $this->notifyEmployee($booking);

            // Notify the user who made the booking
            $this->notifyUser($booking);

            // Return a success response
            return response()->json(['message' => 'Booking created successfully', 'booking' => $booking, 'payment' => $payment], 201);
        } catch (\Exception $e) {
            // Handle any exceptions, such as database errors or invalid employee ID
            return response()->json(['error' => 'Failed to create booking', 'message' => $e->getMessage()], 500);
        }
    }


    protected function notifyEmployee(Booking $booking)
    {
        // Get the employee who needs to be notified about the booking
        $employee = User::findOrFail($booking->employee_id);

        // Pass the employee's name to the notification
        $employeeName = $employee->name;

        // Send notification to the employee, passing the booking and employee's name
        $employee->notify(new BookingNotificationForEmployee($booking, $employeeName));
    }

    protected function notifyUser($booking)
    {
        // Get the user who made the booking
        $user = $booking->user;

        // Get the employee user with the role name "employee"
        $employee = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        })->first();

        // Notify the user about the booking, passing the booking and employee name
        $user->notify(new BookingNotificationForUser($booking, $employee->name));
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

public function history(Request $request)
{
    // Check if the user is authenticated
    if (Auth::check()) {
        // Retrieve authenticated user
        $user = Auth::user();

        // Fetch booking history for the authenticated user
        $bookings = Booking::where('user_id', $user->id)->get();

        // Iterate through each booking to replace employee IDs with names
        $bookings->transform(function ($booking) {
            // Retrieve the employee's name using the employee ID
            $employee = User::whereHas('roles', function ($query) {
                $query->where('name', 'employee');
            })->find($booking->employee_id);

            if ($employee) {
                // If employee found, replace the ID with the name
                $booking->employee_name = $employee->name;
            } else {
                // If employee not found, set the name as null
                $booking->employee_name = null;
            }

            // Retrieve the service name using the service ID
            $service = Service::find($booking->service_id);

            if ($service) {
                // If service found, replace the ID with the name
                $booking->service_name = $service->name;
                $booking->service_price = $service->price;

            } else {
                // If service not found, set the name as null
                $booking->service_name = null;
            }

            // Remove the employee_id and service_id fields from the JSON response
            unset($booking->employee_id);
            unset($booking->service_id);

            return $booking;
        });

        // Return the modified bookings data as JSON response
        return response()->json($bookings);
    } else {
        // User is not authenticated, return an error response
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}

public function Employeehistory(Request $request)
{
    // Check if the user is authenticated
    if (Auth::check()) {
        // Retrieve authenticated user
        $user = Auth::user();

        // Check if the authenticated user is an employee
        if ($user->hasRole('employee')) {
            // Fetch booking history for the authenticated employee
            $bookings = Booking::with(['user', 'service'])
                ->where('employee_id', $user->id)
                ->get();

            // Transform the bookings data to include user name and service name
            $bookings->transform(function ($booking) {
                // Add user name and service name to the booking data
                $booking->customer_name = $booking->user->name;
                $booking->customer_email = $booking->user->email;
                $booking->service_name = $booking->service->name;
                $booking->service_price = $booking->service->price;
                $booking->comment = $booking->comments;



                // Remove the user and service relationships from the booking object
                unset($booking->user);
                unset($booking->service);

                return $booking;
            });

            // Return the bookings data as JSON response
            return response()->json($bookings);
        } else {
            // If the authenticated user is not an employee, return an error response
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    } else {
        // User is not authenticated, return an error response
        return response()->json(['error' => 'Unauthenticated'], 401);
    }
}

public function update(Request $request, $id)
{
    // Validate incoming request data
    $validator = Validator::make($request->all(), [
        'comments' => 'nullable|string',
        'status' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    try {
        // Attempt to find the booking, but use `find` instead of `findOrFail` to manually handle not found
        $booking = Booking::find($id);

        // Check if booking was found
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        // Update booking details
        $booking->comments = $request->comments ?? $booking->comments; // Only update if provided
        $booking->status = $request->status;

        // Save the changes
        $booking->save();

        // Return a success response
        return response()->json(['message' => 'Booking updated successfully', 'booking' => $booking], 200);
    } catch (\Exception $e) {
        // Handle any other exceptions
        return response()->json(['error' => 'Booking update failed', 'message' => $e->getMessage()], 500);
    }
}


}


