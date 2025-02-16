<?php

use App\Models\Customer;
use App\Models\Need;
use App\Models\Organisation;
use App\Models\Property;
use App\Models\Registration;
use App\Models\Representative;
use App\Models\Responsibility;
use App\Models\Service;
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

    Registration::insert([
        'id' => 1,
        'customer' => 1,
        'recipient_name' => 'Recipient Name',
        'source_id' => 1,
        'source_type' => Source::class,
        'consent_date' => '2025-06-11',
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

    Need::insert([
        'id' => 1,
        'registration_id' => 1,
        'code' => 1,
        'lastupdate_id' => 1,
        'lastupdate_type' => Representative::class,
    ]);

    Need::insert([
        'id' => 2,
        'registration_id' => 1,
        'code' => 9,
        'lastupdate_id' => 1,
        'lastupdate_type' => Representative::class,
    ]);

    Need::insert([
        'id' => 3,
        'registration_id' => 1,
        'code' => 32,
        'temp_end_date' => '2025-06-11',
        'lastupdate_id' => 1,
        'lastupdate_type' => Representative::class,
    ]);

    Service::insert([
        'id' => 1,
        'registration_id' => 1,
        'code' => '16P',
        'lastupdate_id' => 1,
        'lastupdate_type' => Representative::class,
    ]);

});


/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    $customer = Customer::find(1);

    $response = $this->getJson("/api/v1/customer/$customer->id");

    $response->assertStatus(401);

});


/**
 * Fail to find a customer that doesn't exist
 */
it('returns an error if the Customer ID is non-existent', function () {

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

