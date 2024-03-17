<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; // Import Role model from Spatie package

class RoleController extends Controller
{
    public function index()
    {
        // Fetch roles from the database
        $roles = Role::all();

        // Return roles as JSON response
        return response()->json(['roles' => $roles]);
    }
}
