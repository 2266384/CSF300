<?php

use App\Models\NeedCode;
use App\Models\Organisation;
use App\Models\Representative;
use App\Models\ServiceCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\get;


// Refresh the database before running
uses(RefreshDatabase::class);

beforeEach(function () {
    // Create a test user with ID 1
    User::factory()->create([
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@example.com',
        'is_admin' => true,
    ]);

    NeedCode::create([
        'code' => '1',
        'description' => 'Nebuliser and Apnoea Monitor',
        'active' => true
    ]);

    ServiceCode::create([
        'code' => 'FBR',
        'description' => 'Braille bill',
        'active' => true
    ]);

    Organisation::insert([
        'id' => 1,
        'name' => 'Test Organisation',
        'active' => true
    ]);

    Representative::insert([
            'id' => 1,
            'name' => 'Test Representative',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'organisation_id' => 1,
            'active' => true]
    );

});


// Load the Protected Routes dataset
dataset('admin_routes', function () {
    // Load dataset from external file
    return require __DIR__ . '/../Datasets/AdminRoutesDataset.php';
});


// Run the test for each of the Routes
it('redirects unauthenticated users to the login page for all Admin GET routes', function (string $route) {
    $response = get($route);

    $response->assertStatus(302)
        ->assertRedirect('/login');
})->with('admin_routes');
