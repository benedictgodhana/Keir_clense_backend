<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;

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

}
