<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function assignServiceToEmployee(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'service_id' => 'required',
                'employee_id' => 'required',
            ]);

            // Find the service
            $service = Service::findOrFail($validatedData['service_id']);

            // Find the employee
            $employee = User::findOrFail($validatedData['employee_id']);

            // Assign the service to the employee
            $employee->services()->attach($service);

            return response()->json(['message' => 'Service assigned to employee successfully'], 200);
        } catch (ValidationException $e) {
            // Return validation errors
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Return error if any other exception occurs
            return response()->json(['message' => 'Failed to assign service to employee'], 500);
        }
    }
}
