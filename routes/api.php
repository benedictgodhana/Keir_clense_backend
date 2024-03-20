<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController; // Add this line to import the ServiceController
use App\Http\Controllers\UserController; // Add this line to import the ServiceController
use App\Http\Controllers\RoleController; // Add this line to import the ServiceController
use App\Http\Controllers\EmployeeController; // Add this line to import the ServiceController
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\BookingController;






/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/services/count', [ServiceController::class, 'getServiceCount']);
Route::get('/users/count', [UserController::class, 'getUserCount']);
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/users', [UserController::class, 'getAllUsers']);
Route::put('/users/{id}', [UserController::class, 'updateUser']);
Route::get('/roles', [RoleController::class, 'index']);
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::put('/services/{id}', [ServiceController::class, 'update']);
Route::post('/services', [ServiceController::class,'store']);
Route::get('/employees', [UserController::class, 'getEmployees']);
Route::post('/employees/assign-service', [UserController::class, 'assignService']);
Route::get('/employees_services', [EmployeeController::class, 'index']);
Route::post('/assign-service', [AssignmentController::class, 'assignServiceToEmployee']);
Route::get('/available-employees', [BookingController::class, 'getAvailableEmployees']);
Route::post('/bookings', [BookingController::class, 'store']);
Route::get('/bookings/employee', [BookingController::class, 'getEmployeeBookings']);



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
