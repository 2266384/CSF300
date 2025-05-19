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

    $response = $this->getJson("/api/v1/customers");

    $response->assertStatus(401);

});


/**
 * Tests an authenticated user can retrieve a valid JSON response
 */
it('returns valid JSON data of Customers for an authenticated user', function () {

    TestCounter::$count++;
    dump(sprintf('Test %03d - Testing it returns valid JSON data of Customers for an authenticated user', TestCounter::$count));

    // Authenticate using Sanctum with read ability
    Sanctum::actingAs(Representative::find(1), ['read']);

    $response = $this->getJson("/api/v1/customers");

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

