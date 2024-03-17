<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

        // Return a success response with the created user
        return response()->json(['user' => $user], 201);
    }

    public function getEmployees()
    {
        // Fetch users with the role name "employee"
        $employees = User::role('employee')->get(['id', 'name', 'email']);

        // Return the response
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

}
