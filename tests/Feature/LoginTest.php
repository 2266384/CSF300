<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\post;
use function Pest\Laravel\assertAuthenticatedAs;
use function Pest\Laravel\assertGuest;


// Refresh the database
uses(RefreshDatabase::class);


// Create an Admin and User for testing
beforeEach(function () {
    $this->admin = User::factory()->create([
        'name' => 'Test Admin',
        'email' => 'test@example.com',
        'password' => 'adminpassword',
        'is_admin' => true,
    ]);

    $this->user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'user@example.com',
        'password' => 'userpassword',
        'is_admin' => false,
    ]);
});


// Try to log in with admin and user - two successes and one failure
it('logs in an admin successfully', function () {
    $response = post('/login', [
        'email' => $this->admin->email,
        'password' => 'adminpassword',
    ]);

    $response->assertRedirect('/home');
    assertAuthenticatedAs($this->admin);
});

it('logs in a user successfully', function () {
    $response = post('/login', [
        'email' => $this->user->email,
        'password' => 'userpassword',
    ]);

    $response->assertRedirect('/home');
    assertAuthenticatedAs($this->user);
});

// Failed login will redirect to the Root and display an error message
it('prevents login with invalid credentials', function () {
    $response = post('/login', [
        'email' => $this->user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertRedirect('/');
    $response->assertSessionHasErrors(['email' => 'These credentials do not match our records.']);
    assertGuest();
});
