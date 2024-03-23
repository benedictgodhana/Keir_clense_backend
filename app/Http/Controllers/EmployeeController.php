<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Service;
use App\Models\User;



class EmployeeController extends Controller
{
    public function index()
{
    $employees = Employee::with(['user', 'service'])->get(['id', 'user_id', 'service_id', 'created_at', 'updated_at']);

    // Transform the collection to include user name, email, and service name instead of IDs
    $transformedEmployees = $employees->map(function ($employee) {
        return [
            'id' => $employee->id,
            'user_name' => $employee->user->name,
            'user_email' => $employee->user->email, // Include the user's email
            'service_name' => $employee->service->name,
            'created_at' => $employee->created_at,
            'updated_at' => $employee->updated_at,
        ];
    });

    return response()->json($transformedEmployees);
}

public function getEmployeesByService($serviceId)
{
    try {
        // Find the service by ID
        $service = Service::findOrFail($serviceId);

        // Get employees associated with the service
        $employees = User::whereHas('roles', function ($query) {
            $query->where('name', 'employee');
        })->whereHas('services', function ($query) use ($serviceId) {
            $query->where('services.id', $serviceId); // Specify the table alias for the id column
        })->get();

        // Return the list of employees as JSON response
        return response()->json(['employees' => $employees], 200);
    } catch (\Exception $e) {
        // Handle any exceptions (e.g., service not found)
        return response()->json(['message' => 'Error fetching employees: ' . $e->getMessage()], 500);
    }
}

public function countEmployees()
{
    // Assuming the role name is 'employee'
    $employeeCount = User::role('employee')->count();

    // Return the count as a JSON response
    return response()->json([
        'employee_count' => $employeeCount,
    ]);
}

public function fetchServices()
    {
        $services = Service::all();

        return response()->json(['services' => $services]);
    }


}
