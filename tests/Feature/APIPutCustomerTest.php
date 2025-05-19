<?php

use App\Helpers\TestCounter;
use App\Models\Customer;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Registration;
use App\Models\Representative;
use App\Models\Responsibility;
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

    // Variable for use in the different tests
    $this->validPayload = [
        "primary_title" => "Mr.",
        "primary_forename" => "NewForename",
        "primary_surname" => "NewSurname",
        "secondary_title" => "Mrs.",
        "secondary_forename" => "NewForename2",
        "secondary_surname" => "NewSurname2",
        "recipient_name" => "New Recipient Name",
        "consent_date" => "2026-01-01",
        "needs" => [
            ["code" => 8, "end_date" => ""],
            ["code" => 14, "end_date" => ""],
            ["code" => 32, "end_date" => "2025-12-31"],
            ["code" => 37, "end_date" => ""],
        ]
    ];

    $this->invalidPayload = [
        "primary_title" => "Prof.",
        "primary_forename" => "Eldorrra",
        "primary_surname" => "Haag",
        "secondary_title" => "Prof.",
        "secondary_forename" => "Kaleee",
        "secondary_surname" => "Waelchi",
        "recipient_name" => "Recipient Name",
        "consent_date" => "2026-01-01",
        "needs" => [
        ],
    ];

    $this->id = 1;

});




/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it gets redirected to the login page if not authenticated', TestCounter::$count));

    $response = $this->putJson("/api/v1/customer/{$this->id}", $this->validPayload);

    $response->assertStatus(401);

});


/**
 * Tests an authenticated user can't update a registration with invalid data
 */
it('returns an error if the data is invalid', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns an error if the data is invalid', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->putJson("/api/v1/customer/{$this->id}", $this->invalidPayload);

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => 422,
            "message" => "Validation failed",
            "errors" => [
                "needs" => [
                    "Customer must have at least one need",
                ]
            ]
        ]);

});


/**
 * Tests an authenticated user can update a Customer record
 */
it('allows an authenticated user to update a Customer record', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it allows an authenticated user to update a Customer record', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->putJson("/api/v1/customer/{$this->id}", $this->validPayload);

    $response->assertStatus(200)
        ->assertJson([
            "status" => 200,
            "message" => "Customer updated successfully",
        ]);

});
