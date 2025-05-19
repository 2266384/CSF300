<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\RegistrationController;
use App\Http\Controllers\Api\ResponsibilityController;
use App\Http\Middleware\IsInternalUserMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



//Route::middleware(['auth:sanctum', 'ability:read'])->get('/user', [\App\Http\Controllers\Api\UserController::class, 'index']);

// Includes the internal.php configuration so that we can access the internal routes using the api/v1 path
Route::prefix('internal')->group(function () {
    require base_path('routes/internal.php');
});


// READ access authorisation group
Route::middleware(['auth:sanctum', 'ability:read'])->group(function () {

    // Returns full list of customers in occupancy in the properties that the requestors organisation is responsbile for
    Route::get('/customers', [CustomerController::class, 'index']);

    // Returns a singe customer in occupancy in a property the requestors oeganisation is responsible for
    Route::get('/customer/{id}', [CustomerController::class, 'show']);

    // Return full list of properties the requestors organisation is responsible for
    Route::get('/properties', [PropertyController::class, 'index']);

    // Gets a single property the requestor is responsible for
    Route::get('/property/{id}', [PropertyController::class, 'show']);
    Route::get('/property', [PropertyController::class, 'getPropertyByQuery']);     // Accepts query parameters for property

});


// WRITE access authorisation group
Route::middleware(['auth:sanctum', 'ability:write'])->group(function () {

    // Create a single new registration
    Route::post('/customer', [RegistrationController::class, 'store']);
    Route::post('/customers', [RegistrationController::class, 'storeAll']);

    // Register a postcode as one the organisation is responsible for
    Route::post('/responsibility', [ResponsibilityController::class, 'store']);
    Route::post('/responsibilities', [ResponsibilityController::class, 'storeAll']);
    Route::delete('/responsibility', [ResponsibilityController::class, 'destroy']);
    Route::delete('/responsibilities', [ResponsibilityController::class, 'destroyAll']);

    // Update a customer record - additive process
    Route::put('/customer/{id}', [CustomerController::class, 'update']);
    Route::put('/customers', [CustomerController::class, 'updateAll']);

});


// Internal API's
    Route::post('/bulkCustomer', [CustomerController::class, 'updateAll']);
        //->middleware(IsInternalUserMiddleware::class);



