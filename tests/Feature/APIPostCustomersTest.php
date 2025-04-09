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
        ],
        [
            "primary_title" => "Mr.",
            "primary_forename" => "Forename3",
            "primary_surname" => "Surname3",
            "secondary_title" => "",
            "secondary_forename" => "",
            "secondary_surname" => "",
            "recipient_name" => "Recipient Name 2",
            "consent_date" => "2028-01-01",
            "needs" => [
                ["code" => 2, "end_date" => ""],
                ["code" => 9, "end_date" => ""],
                ["code" => 33, "end_date" => "2027-12-31"],
                ["code" => 37, "end_date" => ""],
            ],
            "property" => [
                "id" => 2,
                "uprn" => 1023456789,
                "houseno" => "6",
                "housename" => "The House",
                "street" => "Broad",
                "town" => "MyTown",
                "parish" => "",
                "county" => "",
                "postcode" => "NP11 3AB",
            ]
        ]

    ];

    // Property for customer two does not exist
    $this->invalidPropertyPayload = [
        [
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
        ],
        [
            "primary_title" => "Mr.",
            "primary_forename" => "Forename3",
            "primary_surname" => "Surname3",
            "secondary_title" => "",
            "secondary_forename" => "",
            "secondary_surname" => "",
            "recipient_name" => "Recipient Name 2",
            "consent_date" => "2028-01-01",
            "needs" => [
                ["code" => 2, "end_date" => ""],
                ["code" => 9, "end_date" => ""],
                ["code" => 33, "end_date" => "2027-12-31"],
                ["code" => 37, "end_date" => ""],
            ],
            "property" => [
                "id" => 3,
                "uprn" => 1234506789,
                "houseno" => "35B",
                "housename" => "",
                "street" => "Long Lane",
                "town" => "MyTown",
                "parish" => "",
                "county" => "",
                "postcode" => "CF12 3AB",
            ]
        ]

    ];

    // Customer one data doesn't match current occupier
    $this->invalidCustomerPayload = [
        [
            "primary_title" => "Mrs.",
            "primary_forename" => "J",
            "primary_surname" => "Smith",
            "secondary_title" => "Dr.",
            "secondary_forename" => "Arthur",
            "secondary_surname" => "Jones",
            "recipient_name" => "Recipient Name",
            "consent_date" => "2025-06-14",
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
        ],
        [
            "primary_title" => "Mr.",
            "primary_forename" => "Forename3",
            "primary_surname" => "Surname3",
            "secondary_title" => "",
            "secondary_forename" => "",
            "secondary_surname" => "",
            "recipient_name" => "Recipient Name 2",
            "consent_date" => "2028-01-01",
            "needs" => [
                ["code" => 2, "end_date" => ""],
                ["code" => 9, "end_date" => ""],
                ["code" => 33, "end_date" => "2027-12-31"],
                ["code" => 37, "end_date" => ""],
            ],
            "property" => [
                "id" => 2,
                "uprn" => 1023456789,
                "houseno" => "6",
                "housename" => "The House",
                "street" => "Broad",
                "town" => "MyTown",
                "parish" => "",
                "county" => "",
                "postcode" => "NP11 3AB",
            ]
        ]

    ];

    // Customer one data doesn't match current occupier and Property does not exist
    $this->invalidPayload = [
        [
            "primary_title" => "Mrs.",
            "primary_forename" => "J",
            "primary_surname" => "Smith",
            "secondary_title" => "Dr.",
            "secondary_forename" => "Arthur",
            "secondary_surname" => "Jones",
            "recipient_name" => "Recipient Name",
            "consent_date" => "2025-06-14",
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
        ],
        [
            "primary_title" => "Mr.",
            "primary_forename" => "Forename3",
            "primary_surname" => "Surname3",
            "secondary_title" => "",
            "secondary_forename" => "",
            "secondary_surname" => "",
            "recipient_name" => "Recipient Name 2",
            "consent_date" => "2028-01-01",
            "needs" => [
                ["code" => 2, "end_date" => ""],
                ["code" => 9, "end_date" => ""],
                ["code" => 33, "end_date" => "2027-12-31"],
                ["code" => 37, "end_date" => ""],
            ],
            "property" => [
                "id" => 3,
                "uprn" => 1234506789,
                "houseno" => "35B",
                "housename" => "",
                "street" => "Long Lane",
                "town" => "MyTown",
                "parish" => "",
                "county" => "",
                "postcode" => "CF12 3AB",
            ]
        ]

    ];

});




/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    $response = $this->postJson('/api/v1/customers', $this->validPayload);

    $response->assertStatus(401);

});


/**
 * Tests bulk updates for an authenticated user
 * First registration successful
 * Second registration failed as no match to Property
 */
it('returns a success message and an error message if one of the properties does not exist', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson('/api/v1/customers', $this->invalidPropertyPayload);

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => "failed",
            "submitted" => [
                0 => [
                    "status" => 200,
                    "message" => "Registration created successfully",
                    ],
                1 => [
                    "status" => 422,
                    "message" => "Property match cannot be found",
                ]
            ]
        ]);

});


/**
 * Tests bulk updates for an authenticated user
 * First registration failed to match Customer
 * Second registration successful
 */
it('returns a success message and an error message if one of the customers does not match', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson('/api/v1/customers', $this->invalidCustomerPayload);

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => "failed",
            "submitted" => [
                0 => [
                    "status" => 422,
                    "message" => "Customer match cannot be found",
                ],
                1 => [
                    "status" => 200,
                    "message" => "Registration created successfully",
                ]
            ]
        ]);

});


/**
 * Tests bulk updates for an authenticated user
 * First registration failed to match Customer
 * Second registration failed to no matching property
 */
it('returns error messages for both updates', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson('/api/v1/customers', $this->invalidPayload);

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => "failed",
            "submitted" => [
                0 => [
                    "status" => 422,
                    "message" => "Customer match cannot be found",
                ],
                1 => [
                    "status" => 422,
                    "message" => "Property match cannot be found",
                ]
            ]
        ]);

});


/**
 * Tests bulk updates for an authenticated user
 * First and Second Customer updated successfully
 */
it('returns a success message for multiple customer updates', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->postJson('/api/v1/customers', $this->validPayload);

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => "success",
            "submitted" => [
                0 => [
                    "status" => 200,
                    "message" => "Registration created successfully",
                ],
                1 => [
                    "status" => 200,
                    "message" => "Registration created successfully",
                ]
            ]
        ]);

});

