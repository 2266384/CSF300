<?php

use App\Helpers\TestCounter;
use App\Models\Customer;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Representative;
use App\Models\Responsibility;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\SourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;


beforeEach(function () {

    // Clear the database and seed it with test data
    Artisan::call('migrate:fresh');
    (new Tests\Seeders\PestTestSeeder)->run();

    // Variable for use in the different tests
    $this->validPayload = [
        'CF12 3AB',
        'NP11 3AB',
    ];

    $this->invalidPayload = [
        'CF12 3AB',
        'SA7 6TY'
    ];

    $this->noPostcodePayload = [
        'CF1 9AB',
        'NP12 2FE',
    ];

});


/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it gets redirected to the login page if not authenticated', TestCounter::$count));

    $response = $this->deleteJson("/api/v1/responsibilities", $this->validPayload);

    $response->assertStatus(401);

});


/**
 * Tests bulk deletions for an authenticated user
 * First Responsibility successful
 * Second Responsibility does not exist
 */
it('returns a failure message and success message for multiple updates', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns a failure message and success message for multiple updates', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->deleteJson('/api/v1/responsibilities', $this->invalidPayload);

    //dd($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => "failed",
            "submitted" => [
                0 => [
                    "status" => 200,
                    "message" => "Responsibility removed successfully",
                    //"error" => [],
                ],
                1 => [
                    "status" => 422,
                    "message" => "Responsibility does not exist",
                    //"error" => [],
                ]
            ]
        ]);
});


/**
 * Tests bulk deletions for an authenticated user
 * First Responsibility does not exist
 * Second Postcode does not exist
 */
it('returns two failure messages for multiple updates', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns two failure messages for multiple updates', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->deleteJson('/api/v1/responsibilities', $this->noPostcodePayload);

    //dd($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => "failed",
            "submitted" => [
                0 => [
                    "status" => 422,
                    "message" => "Responsibility does not exist",
                    //"error" => [],
                ],
                1 => [
                    "status" => 422,
                    "message" => "Responsibility does not exist",
                    //"error" => [],
                ]
            ]
        ]);
});


/**
 * Tests bulk deletions for an authenticated user
 * First and Second Responsibility updated successfully
 */
it('returns a success message for multiple responsibility deletions', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns a success message for multiple responsibility deletions', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->deleteJson('/api/v1/responsibilities', $this->validPayload);

    //dd($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => "success",
            "submitted" => [
                0 => [
                    "status" => 200,
                    "message" => "Responsibility removed successfully",
                ],
                1 => [
                    "status" => 200,
                    "message" => "Responsibility removed successfully",
                ]
            ]
        ]);
});
