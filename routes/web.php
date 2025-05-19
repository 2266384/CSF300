<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MetricsController;
use App\Http\Controllers\NeedCodeController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\RepresentativeController;
use App\Http\Controllers\ServiceCodeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailController;

use App\Http\Middleware\IsAdminMiddleWare;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/*
 * Register authorisation Routes
 * Disable Registration, Reset, and Verify Routes
 */
Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
    ]);


// Blade for testing - needs to be removed for final prod
Route::get('/test2', function () {
    return view('test2');
});


Route::get('/test-searchable', function () {
    $property = Property::find(1);  // Use an existing property ID
    dd($property->toSearchableArray());
});

/*
 * Routes that don't require authorisation
 */
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('login_home')->middleware('guest');

// Need to put this behind middleware if it works from here
Route::post('/bulkCustomer', [App\Http\Controllers\Api\CustomerController::class, 'updateAll'])->name('bulkUpdate');

/*
 * Routes that need authorisation to access
 * Include in Middleware Group
 */
Route::middleware(['auth'])
    ->middleware('auth:sanctum')
    ->group(function () {

    // Home screen Dashboard
    Route::get('/home', function () {
        return view('home');
    })->name('home');


    // Blade for testing - needs to be removed for final prod
    Route::get('/test', function () {
        return view('test');
    });


    // Bulk Updates - functionality to be finished
    Route::get('/bulk', function () {
        return view('bulk');
    })->name('bulk');



    // Email form
    Route::get('/report', function () { return view('forms.report'); })->name('report');
    Route::post('/send-email', [EmailController::class, 'sendEmail'])->name('send.email');



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
    Route::post('/organisation-update', [OrganisationController::class, 'update']);

    Route::get('/search', [CustomerController::class, 'search'])->name('search');

    // Check all functionality works without this route
    //Route::get('/actions', [AttributeController::class, 'actions'])->name('actions');



});





// User must be authorised and Admin to access these routes
Route::middleware(['auth'])
    ->middleware('auth:sanctum')
    ->middleware(IsAdminMiddleWare::class)->group(function () {

    Route::view('/admin', 'admin')->name('admin');

    /**
     * Javascript function routes
     */
    Route::post('/create-new-need', [NeedCodeController::class, 'store']);
    Route::post('/create-new-service', [ServiceCodeController::class, 'store']);
    Route::post('/create-new-organisation', [OrganisationController::class, 'store']);
    Route::post('/create-new-representative', [RepresentativeController::class, 'store']);


    // User Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::post('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // NeedCode Routes
    Route::get('/needcodes', [NeedCodeController::class, 'index'])->name('needcodes.index');
    Route::get('/needcodes/{needcode}/edit', [NeedCodeController::class, 'edit'])->name('needcodes.edit');
    Route::post('/needcodes/{needcode}/update', [NeedCodeController::class, 'update'])->name('needcodes.update');
    Route::get('/needcodes/create', [NeedCodeController::class, 'create'])->name('needcodes.create');

    // ServiceCode Routes
    Route::get('/servicecodes', [ServiceCodeController::class, 'index'])->name('servicecodes.index');
    Route::get('/servicecodes/{servicecode}/edit', [ServiceCodeController::class, 'edit'])->name('servicecodes.edit');
    Route::post('servicecodes/{servicecode}/update', [ServiceCodeController::class, 'update'])->name('servicecodes.update');
    Route::get('/servicecodes/create', [ServiceCodeController::class, 'create'])->name('servicecodes.create');

    // Organisations Routes
    Route::get('/organisations', [OrganisationController::class, 'index'])->name('organisations.index');
    Route::get('/organisations/create', [OrganisationController::class, 'create'])->name('organisations.create');
    Route::get('/organisations/{organisation}', [OrganisationController::class, 'show'])->name('organisations.show');
    Route::get('/organisations/{organisation}/edit', [OrganisationController::class, 'edit'])->name('organisations.edit');
    //Route::post('/organisations/{organisation}/update', [OrganisationController::class, 'update'])->name('organisations.update');

    // Representatives Routes
    Route::get('/representatives', [RepresentativeController::class, 'index'])->name('representatives.index');
    Route::get('/representatives/create', [RepresentativeController::class, 'create'])->name('representatives.create');
    Route::get('/representatives/{representative}', [RepresentativeController::class, 'show'])->name('representatives.show');
    Route::get('/representatives/{representative}/edit', [RepresentativeController::class, 'edit'])->name('representatives.edit');
    Route::post('/representatives/{representative}/update', [RepresentativeController::class, 'update'])->name('representatives.update');

    Route::get('/metrics', [MetricsController::class, 'index'])->name('metrics.index');


    /**
     * Route to generate random string for token refresh
     * Used to mimic Sanctum method for API Token so a new token can be assigned to a representative
     * without needing to commit it to the database first in case a user cancels updating the model
     */
    Route::post('/refresh-token', function () {
        return response()->json(['token' => Str::random(40)]);
    });

});










