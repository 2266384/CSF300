<?php

namespace Tests\Feature;

use Tests\TestCase;

class RedirectTest extends TestCase
{

    // PHPUnit Test format
    /**
     * Test if a guest user is redirected to the login page when accessing a protected route.
     */
    public function test_guest_is_redirected_to_login()
    {
        // Attempt to access a protected route without authentication
        $response = $this->get('/home'); // Replace with your protected route

        // Assert the user is redirected to the login page
        $response->assertRedirect(route('login')); // Ensure the route name matches your app
    }

}
