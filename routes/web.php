<?php

use App\Http\Controllers\AttributeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/test', function () {
    return view('test');
});

/**
 * JS function routes
 */
//Route::post('/update-attribute', [AttributeController::class, 'updateAttributes']);
Route::post('/registrations-store', [RegistrationController::class, 'store']);
Route::post('/customer-update', [CustomerController::class, 'update']);
Route::get('/search', [CustomerController::class, 'search'])->name('search');
Route::get('/actions', [AttributeController::class, 'actions'])->name('actions');



Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');

Route::get('/registrations/create/{customer}', [RegistrationController::class, 'create'])
    ->name('registrations.create');


Route::get('/users', [UserController::class, 'index'])->name('users.index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('login_home');

Route::view('/admin', 'admin')->name('admin');
