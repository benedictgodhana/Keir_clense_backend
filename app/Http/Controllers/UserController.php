<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Employee;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUserCount()
    {
        $userCount = user::count();

        return response()->json(['count' => $userCount]);
    }

    public function getAllUsers()
    {
        $users = User::with('roles')->get();

        return response()->json(['users' => $users]);
    }

    public function updateUser(Request $request, $id)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|string|exists:roles,name', // Check if the role exists
            'password' => 'nullable|string|min:6', // Allow password to be nullable and minimum length 6
            // Add more validation rules as needed for other fields
        ]);

        // Find the user by id
        $user = User::findOrFail($id);

        // Update the user data
        $user->update([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            // Update other fields as needed
        ]);

        // Update password if provided
        if (isset($validatedData['password'])) {
            $user->update([
                'password' => bcrypt($validatedData['password']), // Hash the password
            ]);
        }

        // Find the role by name
        $role = Role::where('name', $validatedData['role'])->first();

        // Sync the user's roles
        $user->syncRoles([$role]);

        return response()->json(['message' => 'User updated successfully']);
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|exists:roles,name',
            'password' => 'required|string|min:8',
            'service_id' => 'required_if:role,employee|exists:services,id', // Validate service ID only if the role is employee
            // Add more validation rules for other fields if needed
        ]);

        // Check if the role exists
        if (!Role::where('name', $validatedData['role'])->exists()) {
            return response()->json(['error' => 'Invalid role'], 422);
        }

        // Create the new user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Assign the role to the user
        $user->assignRole($validatedData['role']);

        // If the role is employee, associate the user with the service
        if ($validatedData['role'] === 'employee') {
            // Find the service
            $service = Service::findOrFail($validatedData['service_id']);

            // Create an employee record and associate the user with the service
            $employee = Employee::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
            ]);
        }

        // Return a success response with the created user
        return response()->json(['user' => $user], 201);
    }

    public function getEmployees()
{
    // Fetch users with the role "employee" and eager load their associated services
    $employees = User::role('employee')->with('services')->get()->map(function ($user) {
        // Simplify the service information, if needed
        $user->services = $user->services->map(function ($service) {
            return [
                'id' => $service->id,
                'name' => $service->name
            ];
        });
        return $user;
    });

    return response()->json(['employees' => $employees]);
}


    public function assignService(Request $request)
    {
        // Validate the request

        $employee = User::find($request->input('employee_id'));

        // Assign service to the employee
        // Your logic to assign service here...

        return response()->json(['message' => 'Service assigned successfully']);
    }

    public function countCustomers()
{
    // Assuming the role name is 'employee'
    $customerCount = User::role('customer')->count();

    // Return the count as a JSON response
    return response()->json([
        'customer_count' => $customerCount,
    ]);
}


public function destroy($id)
{
    // Find user
    $user = User::findOrFail($id);

    // Delete user
    $user->delete();

    return response()->json(null, 204);
}

}
