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

// Automated test fails due to difference in SQL types between Prod and Test

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

afterEach(function () {
    (new SourcesTableSeeder())->run();
});



it('authenticate representative can GET Property using parameters from api with bearer token and structure matches', function () {

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read']);

    $response = $this->getJson("/api/v1/property?postcode=CF12 3AB");

    dump($response->json());

    $response->assertStatus(200)
        ->assertJsonStructure([
            '*' => [ // `*` means multiple items (array)
                'ID',
                'UPRN',
                'House No',
                'House Name',
                'Street',
                'Town',
                'Parish',
                'County',
                'Postcode'
            ]
        ]);
});

