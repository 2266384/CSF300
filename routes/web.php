<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;

use App\Http\Middleware\IsAdminMiddleWare;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
 * Register authorisation Routes
 * Disable Registration, Reset, and Verify Routes
 */
Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
    ]);



/*
 * Routes that don't require authorisation
 */
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('login_home')->middleware('guest');



/*
 * Routes that need authorisation to access
 * Include in Middleware Group
 */
Route::middleware(['auth'])->group(function () {

    // Home screen Dashboard
    Route::get('/home', function () {
        return view('home');
    })->name('home');


    // Blade for testing - needs to be removed for final prod
    Route::get('/test', function () {
        return view('test');
    });




    // Customers variable is passed by the search JS function which redirects here
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::post('/customers/{customer}/update', [CustomerController::class, 'update'])->name('customers.update');


    Route::get('/registrations/create/{customer}', [RegistrationController::class, 'create'])
        ->name('registrations.create');


    /**
     * Javascript function routes
     */
    //Route::post('/update-attribute', [AttributeController::class, 'updateAttributes']);
    Route::post('/registrations-store', [RegistrationController::class, 'store']);
    Route::post('/customer-update', [CustomerController::class, 'update']);
    Route::get('/search', [CustomerController::class, 'search'])->name('search');
    Route::get('/actions', [AttributeController::class, 'actions'])->name('actions');


});





// User must be authorised and Admin to access these routes
Route::middleware(['auth'])->middleware(IsAdminMiddleware::class)->group(function () {

    Route::view('/admin', 'admin')->name('admin');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');

});










