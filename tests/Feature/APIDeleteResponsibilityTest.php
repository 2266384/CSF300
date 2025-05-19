<?php

use App\Helpers\TestCounter;
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

    $this->apiParam = "?postcode=CF12 3AB";

    $this->apiInvalidParam = "?postcode=NP11 3AB";

    $this->apiNonexistentParam = "?postcode=JJ12 1ZC";

});


/**
 * Test an unauthenticated user can't access the API
 */
it('gets redirected to the login page if not authenticated', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it gets redirected to the login page if not authenticated', TestCounter::$count));

    $response = $this->deleteJson("/api/v1/responsibility{$this->apiParam}");

    $response->assertStatus(401);

});


/**
 * Tests deleting a responsibility for an authenticated user
 */
it('returns an error message if the postcode is not registered', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns an error message if the postcode is not registered', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->deleteJson("/api/v1/responsibility{$this->apiInvalidParam}");

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => 422,
            "message" => "Responsibility does not exist",
        ]);

});


/**
 * Tests deleting a responsibility for an authenticated user
 */
it('returns an error message if the postcode does not exist', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns an error message if the postcode does not exist', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->deleteJson("/api/v1/responsibility{$this->apiNonexistentParam}");

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => 422,
            "message" => "Responsibility does not exist",
        ]);

});


/**
 * Tests deleting a responsibility for an authenticated user
 */
it('returns a success message if the postcode is registered', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns a success message if the postcode is registered', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read', 'write']);

    $response = $this->deleteJson("/api/v1/responsibility{$this->apiParam}");

    //dump($response->json());

    $response->assertStatus(200)
        ->assertJson([
            "status" => 200,
            "message" => "Responsibility removed successfully",
        ]);

});
