<?php

use App\Helpers\TestCounter;
use App\Models\Customer;
use App\Models\Need;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Registration;
use App\Models\Representative;
use App\Models\Responsibility;
use App\Models\Service;
use App\Models\Source;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\NeedCodesTableSeeder;
use Database\Seeders\ServiceCodesTableSeeder;
use Database\Seeders\SourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;


beforeEach(function () {

    // Clear the database and seed it with test data
    Artisan::call('migrate:fresh');
    (new Tests\Seeders\PestTestSeeder)->run();

});

afterEach(function () {

    // Clear the database and seed it with test data
    Artisan::call('migrate:fresh');
    (new Tests\Seeders\PestTestSeeder)->run();

});


/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it gets redirected to the login page if not authenticated', TestCounter::$count));

    $property = Property::find(1);

    $response = $this->getJson("/api/v1/property/{$property->id}");

    $response->assertStatus(401);

});


/**
 * Fail to find a property that doesn't exist
 */
it('returns an error if the Property ID is non-existent', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns an error if the Property ID is non-existent', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read']);

    // Use customer ID 100 which doesn't exist in our test data
    $response = $this->getJson("/api/v1/property/100");

    $response->assertStatus(422)
        ->assertExactJson([
            "status" => "error",
            "message" => "Validation failed",
            "errors" => [
                "id" => [
                    "ID does not exist"
                ]
            ]
        ]);

});


/**
 * Tests an authenticated user can retrieve a valid JSON response
 */
it('returns valid JSON data for a single Property record for an authenticated user', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns valid JSON data for a single Property record for an authenticated user', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read']);

    $property = Property::find(1);

    $response = $this->getJson("/api/v1/property/{$property->id}");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'ID',
            'UPRN',
            'House No',
            'House Name',
            'Street',
            'Town',
            'Parish',
            'County',
            'Postcode'
        ]);
});

