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

    $this->apiParam = "?postcode=NP11 3AB";

    $this->apiInvalidParam = "?postcode=CF12 3AB";

    $this->apiNonexistentParam = "?postcode=JJ12 1ZC";

});


/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it gets redirected to the login page if not authenticated', TestCounter::$count));

    $response = $this->postJson("/api/v1/responsibility{$this->apiParam}");

    $response->assertStatus(401);

});


/**
 * Tests creating a responsibility for an authenticated user
 */
it('returns an error message if the postcode is already registered', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns an error message if the postcode is already registered', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson("/api/v1/responsibility{$this->apiInvalidParam}");

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => 422,
            "message" => "Responsibility already exists",
        ]);

});


/**
 * Tests creating a responsibility for an authenticated user where the postcode does not exist
 */
it('returns a validation error message if the postcode does not exist', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns a validation error message if the postcode does not exist', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson("/api/v1/responsibility{$this->apiNonexistentParam}");

    //dump($response->json());

    $response->assertStatus(422)
        ->assertJson([
            "status" => "error",
            "message" => "Validation failed",
            "errors" => [
                "postcode" => [
                    "Postcode does not exist.",
                ]
            ]
        ]);

});


/**
 * Performs a successful registration of the postcode
 */
it('returns a success message when registering a postcode', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns a success message when registering a postcode', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson("/api/v1/responsibility{$this->apiParam}");

    //dump($response->json());

    $response->assertStatus(200);

});
