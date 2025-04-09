<?php

use App\Models\Customer;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Representative;
use App\Models\Responsibility;
use Laravel\Sanctum\Sanctum;
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

    Property::insert([
        'id' => 3,
        'uprn' => 1234056789,
        'house_number' => '34B',
        'house_name' => '',
        'street' => 'Long Lane',
        'town' => 'MyTown',
        'postcode' => 'SA7 6TY',
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

    $response = $this->deleteJson("/api/v1/responsibilities", $this->validPayload);

    $response->assertStatus(401);

});


/**
 * Tests bulk deletions for an authenticated user
 * First Responsibility successful
 * Second Responsibility does not exist
 */
it('returns a failure message and success message for multiple updates', function () {

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
