<?php

use App\Models\Customer;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Representative;
use App\Models\Responsibility;
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

    Responsibility::insert([
        'id' => 1,
        'organisation' => 1,
        'postcode' => 'CF12 3AB'
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
        "primary_title" => "Mr.",
        "primary_forename" => "Forename",
        "primary_surname" => "Surname",
        "secondary_title" => "Mrs.",
        "secondary_forename" => "Forename2",
        "secondary_surname" => "Surname2",
        "recipient_name" => "Recipient Name",
        "consent_date" => "2026-01-01",
        "needs" => [
            ["code" => 1, "end_date" => ""],
            ["code" => 8, "end_date" => ""],
            ["code" => 32, "end_date" => "2025-12-31"],
            ["code" => 37, "end_date" => ""],
        ],
        "property" => [
            "id" => 1,
            "uprn" => 1234567890,
            "houseno" => "14A",
            "housename" => "",
            "street" => "Street Address",
            "town" => "Town",
            "parish" => "",
            "county" => "",
            "postcode" => "CF12 3AB",
        ]

    ];

    $this->invalidPropertyPayload = [
        "primary_title" => "Prof.",
        "primary_forename" => "Eldorrra",
        "primary_surname" => "Haag",
        "secondary_title" => "Prof.",
        "secondary_forename" => "Kaleee",
        "secondary_surname" => "Waelchi",
        "recipient_name" => "Recipient Name",
        "consent_date" => "2026-01-01",
        "needs" => [
            ["code" => 1, "end_date" => ""],
            ["code" => 8, "end_date" => ""],
            ["code" => 32, "end_date" => "2025-12-31"],
            ["code" => 37, "end_date" => ""],
        ],
        "property" => [
            "id" => 2,
            "uprn" => 9876543210,
            "houseno" => "7",
            "housename" => "",
            "street" => "High Street",
            "town" => "",
            "parish" => "",
            "county" => "",
            "postcode" => "NP8 1ZU",
        ]
    ];

    // Variable for use in the different tests
    $this->invalidCustomerPayload = [
        "primary_title" => "Prof.",
        "primary_forename" => "Eldorrra",
        "primary_surname" => "Haag",
        "secondary_title" => "Prof.",
        "secondary_forename" => "Kaleee",
        "secondary_surname" => "Waelchi",
        "recipient_name" => "Recipient Name",
        "consent_date" => "2026-01-01",
        "needs" => [
            ["code" => 1, "end_date" => ""],
            ["code" => 8, "end_date" => ""],
            ["code" => 32, "end_date" => "2025-12-31"],
            ["code" => 37, "end_date" => ""],
        ],
        "property" => [
            "id" => 1,
            "uprn" => 1234567890,
            "houseno" => "14A",
            "housename" => "",
            "street" => "Street Address",
            "town" => "Town",
            "parish" => "",
            "county" => "",
            "postcode" => "CF12 3AB",
        ]

    ];

});




/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    $response = $this->postJson('/api/v1/customer', $this->validPayload);

    $response->assertStatus(401);

});


/**
 * Tests an authenticated user can't create a registration with an invalid property
 */
it('returns an error if the property does not exist', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson('/api/v1/customer', $this->invalidPropertyPayload);

    //dump($response->json());

    $response->assertStatus(200)
    ->assertJson([
        "status" => 422,
        "message" => "Property match cannot be found",
    ]);

});


/**
 * Tests an authenticated user can't create a registration with an invalid customer
 */
it('returns an error if the occupier details do not match', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson('/api/v1/customer', $this->invalidCustomerPayload);

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => 422,
            "message" => "Customer match cannot be found",
        ]);

});


/**
 * Tests an authenticated user can retrieve a valid JSON response
 */
it('allows an authenticated user to create a Customer Registration', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson('/api/v1/customer', $this->validPayload);

    $response->assertStatus(200)
        ->assertJson([
            "status" => 200,
            "message" => "Registration created successfully",
        ]);

});
