<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\User;
use Illuminate\Auth\Events\Validated;
use Illuminate\Validation\ValidationException;

class AssignmentController extends Controller
{
    public function assignServiceToEmployee(Request $request)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'service_id' => 'required',
                'user_id' => 'required',
            ]);

            // Find the employee by ID
            $employee = Employee::findOrFail($validatedData['user_id']);

            // Update the employee's service
            $employee->service()->associate($validatedData['service_id']);
            $employee->save();

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
