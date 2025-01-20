<?php

use App\Models\Customer;
use App\Models\NeedCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;

// Refresh the database
uses(RefreshDatabase::class);


// Create an User for testing
beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'user@example.com',
        'password' => 'userpassword',
        'is_admin' => false,
    ]);

    // Call the necessary Database Seeders
    $this->seed(\Database\Seeders\CustomersTableSeeder::class);
    $this->seed(\Database\Seeders\SourcesTableSeeder::class);
    $this->seed(\Database\Seeders\NeedCodesTableSeeder::class);
    $this->seed(\Database\Seeders\ServiceCodesTableSeeder::class);

});

/*
// Try to log in with a user - and successfully create a registration
it('creates a new registration', function () {
    actingAs($this->user);

    $response = post('/registrations-store' [
        ]);

    $response->assertRedirect('/home');
    assertAuthenticatedAs($this->user);
});
*/
