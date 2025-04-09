<?php

use App\Models\Customer;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Registration;
use App\Models\Representative;
use App\Models\Responsibility;
use App\Models\Source;
use Laravel\Sanctum\Sanctum;
use Database\Seeders\NeedCodesTableSeeder;
use Database\Seeders\ServiceCodesTableSeeder;
use Database\Seeders\SourcesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;


// Refresh the database before running
uses(RefreshDatabase::class);


beforeEach(function () {

    (new SourcesTableSeeder())->run();

    Organisation::insert([
        'id' => 1,
        'name' => 'Test Organisation',
        'active' => true
    ]);

    Customer::insert([
        'id' => 1,
        'SAP_reference' => 9876543210,
        'primary_title' => 'Mr.',
        'primary_forename' => 'Forename',
        'primary_surname' => 'Surname',
        'secondary_title' => 'Mrs.',
        'secondary_forename' => 'Forename2',
        'secondary_surname' => 'Surname2',
    ]);

    Customer::insert([
        'id' => 2,
        'SAP_reference' => 9123456789,
        'primary_title' => 'Mr.',
        'primary_forename' => 'Forename3',
        'primary_surname' => 'Surname3',
        'secondary_title' => '',
        'secondary_forename' => '',
        'secondary_surname' => '',
    ]);

    Registration::insert([
        'id' => 1,
        'customer' => 1,
        'recipient_name' => 'Recipient Name',
        'source_id' => 1,
        'source_type' => Source::class,
        'consent_date' => '2025-06-11',
        'active' => true,
    ]);

    Registration::insert([
        'id' => 2,
        'customer' => 2,
        'recipient_name' => 'Recipient Name 2',
        'source_id' => 1,
        'source_type' => Organisation::class,
        'active' => true,
    ]);

    (new NeedCodesTableSeeder())->run();
    (new ServiceCodesTableSeeder())->run();

    Property::insert([
        'id' => 1,
        'uprn' => 1234567890,
        'house_number' => '14A',
        'street' => 'Street Address',
        'town' => 'Town',
        'postcode' => 'CF12 3AB',
        'occupier' => 1
    ]);

    Property::insert([
        'id' => 2,
        'uprn' => 1023456789,
        'house_number' => '6',
        'house_name' => 'The House',
        'street' => 'Broad Street',
        'town' => 'MyTown',
        'postcode' => 'NP11 3AB',
        'occupier' => 2
    ]);

    Responsibility::insert([
        'id' => 1,
        'organisation' => 1,
        'postcode' => 'CF12 3AB'
    ]);

    Responsibility::insert([
        'id' => 2,
        'organisation' => 1,
        'postcode' => 'NP11 3AB'
    ]);

    Representative::insert([
            'id' => 1,
            'name' => 'Test Representative',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'organisation_id' => 1,
            'active' => true]
    );

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

    $response = $this->putJson("/api/v1/customers", $this->validPayload);

    $response->assertStatus(401);

});


/**
 * Tests an authenticated user can't update a registration with invalid data
 */
it('returns an error if the data is invalid', function () {

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
it('allows an authenticated user to update a Customer record', function () {

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
