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
        [
            "id" => 1,
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
        ],
        [
            "id" => 2,
            "primary_title" => "Mr.",
            "primary_forename" => "NewForename3",
            "primary_surname" => "NewSurname3",
            "secondary_title" => "Mrs.",
            "secondary_forename" => "NewForename4",
            "secondary_surname" => "NewSurname4",
            "recipient_name" => "New Recipient Name 2",
            "consent_date" => "2026-01-01",
            "needs" => [
                ["code" => 1, "end_date" => ""],
                ["code" => 8, "end_date" => ""],
                ["code" => 36, "end_date" => "2026-12-31"],
                ["code" => 37, "end_date" => ""],
            ]
        ]
    ];

    $this->invalidPayload = [
        [
            "id" => 1,
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
        ],
        [
            "id" => 2,
            "primary_title" => "Prof.",
            "primary_forename" => "Eldorrra",
            "primary_surname" => "Haag",
            "secondary_title" => "Prof.",
            "secondary_forename" => "Kaleee",
            "secondary_surname" => "Waelchi",
            "recipient_name" => "Recipient Name",
            "consent_date" => "2026-01-01",
            "needs" => [
                ["code" => 22, "end_date" => ""],
            ],
        ]
    ];

});




/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it gets redirected to the login page if not authenticated', TestCounter::$count));

    $response = $this->putJson("/api/v1/customers", $this->validPayload);

    $response->assertStatus(401);

});


/**
 * Tests an authenticated user can't update a registration with invalid data
 */
it('returns a failure and success message for multiple updates', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns a failure and success message for multiple updates', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->putJson("/api/v1/customers", $this->invalidPayload);

    $response->assertStatus(200)
        ->assertJson([
            "status" => "failed",
            "submitted" => [
                0 => [
                    "status" => 422,
                    "message" => "Validation failed",
                    "errors" => [
                        "needs" => [
                            "Customer must have at least one need",
                        ]
                    ]
                ],
                1 => [
                    "status" => 200,
                    "message" => "Customer updated successfully",
                    "errors" => [],
                ]
            ]
        ]);

});


/**
 * Tests an authenticated user can update a Customer record
 */
it('allows an authenticated user to update multiple Customer records', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it allows an authenticated user to update multiple Customer records', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->putJson("/api/v1/customers", $this->validPayload);

    $response->assertStatus(200)
        ->assertJson([
            "status" => "success",
            "submitted" => [
                0 => [
                    "status" => 200,
                    "message" => "Customer updated successfully",
                    "errors" => [],
                ],
                1 => [
                    "status" => 200,
                    "message" => "Customer updated successfully",
                    "errors" => [],
                ]
            ]
        ]);

});
