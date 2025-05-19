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


// Refresh the database before running
uses(RefreshDatabase::class);


beforeEach(function () {

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

    $customer = Customer::find(1);

    $response = $this->getJson("/api/v1/customer/$customer->id");

    $response->assertStatus(401);

});


/**
 * Fail to find a customer that doesn't exist
 */
it('returns an error if the Customer ID is non-existent', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns an error if the Customer ID is non-existent', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read']);

    // Use customer ID 100 which doesn't exist in our test data
    $response = $this->getJson("/api/v1/customer/100");

    $response->assertStatus(200)
    ->assertJson([
        "status" => 422,
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
it('returns valid JSON data for a single Customer record for an authenticated user', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns valid JSON data for a single Customer record for an authenticated user', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read']);

    $customer = Customer::find(1);

    $response = $this->getJson("/api/v1/customer/$customer->id");

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [ // `*` means multiple items (array)
                'ID',
                'Primary Title',
                'Primary Forename',
                'Primary Surname',
                'Secondary Title',
                'Secondary Forename',
                'Secondary Surname',
                'Properties' => [
                    '*' => [
                        'UPRN',
                        'House No',
                        'House Name',
                        'Street',
                        'Town',
                        'Parish',
                        'County',
                        'Postcode'
                    ]
                ],
                'Needs' => [ // Nested structure
                    '*' => [
                        'Code',
                        'Description',
                        'End Date'
                    ]
                ]
            ]
        ]);
});

