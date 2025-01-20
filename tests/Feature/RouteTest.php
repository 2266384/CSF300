<?php

// PEST Test format

use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;

// Refresh the database before running
uses(RefreshDatabase::class);

// Load the Protected Routes dataset
dataset('protected_routes', function () {
    // Load dataset from external file
    return require __DIR__ . '/../Datasets/ProtectedRoutesDataset.php';
});

// Run the test for each of the Routes
it('redirects unauthenticated users to the login page for all GET routes', function (string $route) {
    $response = get($route);
    $response->assertRedirect(route('login'));
})->with('protected_routes');


