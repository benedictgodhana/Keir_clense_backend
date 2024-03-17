<?php

namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function getServiceCount()
    {
        $serviceCount = Service::count();

        return response()->json(['count' => $serviceCount]);
    }
    public function index()
    {
        $services = Service::all();

        return response()->json($services);
    }

    public function update(Request $request, $id)
{
    // Validate the request data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'price' => 'required|numeric', // Ensure price is a number
    ]);

    // Find the service by id
    $service = Service::findOrFail($id);

    // Update the service data
    $service->update([
        'name' => $validatedData['name'],
        'description' => $validatedData['description'],
        'price' => $validatedData['price'], // Update the price without formatting
        // Update other fields as needed
    ]);

    // Return a success response with the updated service
    return response()->json(['message' => 'Service updated successfully', 'service' => $service]);
}
public function store(Request $request)
{
    // Validate the request data
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:255',
        'price' => 'required|numeric', // Ensure price is a number
    ]);

    // Create the new service
    $service = Service::create([
        'name' => $validatedData['name'],
        'description' => $validatedData['description'],
        'price' => $validatedData['price'],
        // Add other fields as needed
    ]);

    // Return a success response with the newly created service
    return response()->json(['message' => 'Service created successfully', 'service' => $service]);
}


}
