<?php

use App\Http\Controllers\Api\IncidentController;
use App\Http\Middleware\ValidateInternalServiceTokenMiddleware;
use Illuminate\Support\Facades\Route;


// Internal authorisation group
Route::middleware([ValidateInternalServiceTokenMiddleware::class])->group(function () {

    // Return list of registrants within provided postcode regions along with Needs/Services
    Route::get('/report',[IncidentController::class, 'show']);

    //Route::get('/report',[IncidentController::class, 'get']);
});
